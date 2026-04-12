<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'total_price' => (float) $this->total_price,
            'shipping_address' => $this->shipping_address,
            'approvals' => [
                'seller' => $this->seller_approval,
                'admin' => $this->admin_approval,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'buyer' => $this->whenLoaded('user', function () {
                return $this->userPayload($this->user);
            }),
            'seller' => $this->when(
                $this->relationLoaded('product')
                    && $this->product
                    && $this->product->relationLoaded('seller')
                    && $this->product->seller,
                function () {
                    return $this->userPayload($this->product->seller);
                }
            ),
            'product' => $this->whenLoaded('product', function () {
                $product = $this->product;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'brand' => $product->brand,
                    'model' => $product->model,
                    'description' => $product->description,
                    'price' => (float) $product->price,
                    'condition' => $product->condition,
                    'status' => $product->status,
                    'source' => $product->source,
                    'color' => $product->color,
                    'location' => $product->location,
                    'primary_image_url' => $product->primary_image_url,
                    'category' => $product->relationLoaded('category') && $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'images' => $product->relationLoaded('images')
                        ? $product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/'.$image->image_path),
                                'is_primary' => (bool) $image->is_primary,
                            ];
                        })->values()->all()
                        : [],
                ];
            }),
            'variant' => $this->whenLoaded('variant', function () {
                if (! $this->variant) {
                    return null;
                }

                return [
                    'id' => $this->variant->id,
                    'color_name' => $this->variant->color_name,
                    'color_code' => $this->variant->color_code,
                    'stock_quantity' => (int) $this->variant->stock_quantity,
                ];
            }),
        ];
    }

    private function userPayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'phone' => $user->phone,
            'location' => $user->location,
        ];
    }
}
