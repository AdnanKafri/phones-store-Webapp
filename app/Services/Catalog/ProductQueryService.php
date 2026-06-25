<?php

namespace App\Services\Catalog;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductQueryService
{
    public function getPublicProducts(array $filters = [], int $perPage = 12, array $relations = ['category', 'seller']): LengthAwarePaginator
    {
        $query = Product::with($relations);

        if (! empty($filters['source']) && in_array($filters['source'], ['inventory', 'user'], true)) {
            $query->where('source', $filters['source']);
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== 'all' && $filters['status'] !== '' && ! is_null($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', '!=', 'pending');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getApiPublicProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->getPublicProducts($filters, $perPage, $this->apiRelations());
    }

    public function loadPublicProduct(Product $product, array $relations = ['seller', 'category', 'reviews.user', 'variants']): Product
    {
        return $product->load($relations);
    }

    public function loadApiPublicProduct(Product $product): Product
    {
        return $product->load($this->apiRelations());
    }

    public function getRelatedProducts(Product $product, int $limit = 4, array $relations = ['seller']): Collection
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'available')
            ->with($relations)
            ->take($limit)
            ->get();
    }

    public function getUserListings(int $userId, int $perPage = 12, array $relations = ['category']): LengthAwarePaginator
    {
        return Product::where('user_id', $userId)
            ->with($relations)
            ->latest()
            ->paginate($perPage);
    }

    public function getApiUserListings(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->getUserListings($userId, $perPage, $this->apiRelations());
    }

    public function loadListingForEdit(Product $product): Product
    {
        return $product->load('images', 'category');
    }

    private function apiRelations(): array
    {
        return ['seller', 'category', 'device', 'images', 'variants'];
    }
}
