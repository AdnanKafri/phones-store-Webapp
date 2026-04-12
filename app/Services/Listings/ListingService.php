<?php

namespace App\Services\Listings;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingService
{
    public function canManage(Product $product, User $user): bool
    {
        return $product->user_id === $user->id;
    }

    public function createUserListing(array $validated, User $user): Product
    {
        $name = $validated['brand'].' '.$validated['model'];
        $slug = Str::slug($name).'-'.uniqid();

        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';
        $validated['source'] = 'user';
        $validated['name'] = $name;
        $validated['slug'] = $slug;
        $validated['disassembled_is'] = ! empty($validated['disassembled_is']) ? 1 : 0;

        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $product = Product::create($validated);

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return $product;
    }

    public function updateUserListing(
        Product $product,
        array $validated,
        User $user,
        array $newImages = [],
        array $deleteImageIds = [],
    ): array {
        if (! $this->canManage($product, $user)) {
            return $this->failure('This action is unauthorized.', 'FORBIDDEN');
        }

        $name = $validated['brand'].' '.$validated['model'];
        $validated['name'] = $name;
        $validated['slug'] = Str::slug($name).'-'.$product->id;
        $validated['disassembled_is'] = ! empty($validated['disassembled_is']) ? 1 : 0;

        unset($validated['images'], $validated['delete_images']);

        if ($deleteImageIds !== []) {
            foreach ($deleteImageIds as $imageId) {
                $image = $product->images()->find($imageId);

                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($newImages !== []) {
            $currentImageCount = $product->images()->count();
            $newImageCount = count($newImages);

            if ($currentImageCount + $newImageCount > 5) {
                return $this->failure(
                    'لا يمكن أن يتجاوز مجموع الصور 5 صور. يرجى حذف بعض الصور القديمة أولاً.',
                    'LISTING_IMAGE_LIMIT_EXCEEDED'
                );
            }

            foreach ($newImages as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
        }

        $product->update($validated);

        return [
            'success' => true,
            'product' => $product,
        ];
    }

    public function deleteUserListing(Product $product, User $user): array
    {
        if (! $this->canManage($product, $user)) {
            return $this->failure('This action is unauthorized.', 'FORBIDDEN');
        }

        $product->images()->delete();
        $product->delete();

        return ['success' => true];
    }

    private function failure(string $message, string $code): array
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => $code,
        ];
    }
}
