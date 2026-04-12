<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'model' => $this->model,
            'description' => $this->description,
            'defects' => $this->defects,
            'condition_notes' => $this->condition_notes,
            'accessories' => $this->accessories,
            'disassembled_is' => (bool) $this->disassembled_is,
            'reason_disassembly' => $this->reason_disassembly,
            'price' => (float) $this->price,
            'condition' => $this->condition,
            'status' => $this->status,
            'source' => $this->source,
            'color' => $this->color,
            'location' => $this->location,
            'primary_image_url' => $this->primary_image_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'seller' => $this->whenLoaded('seller', function () {
                return [
                    'id' => $this->seller->id,
                    'name' => $this->seller->name,
                    'username' => $this->seller->username,
                    'location' => $this->seller->location,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('storage/'.$image->image_path),
                        'is_primary' => (bool) $image->is_primary,
                    ];
                })->values()->all();
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color_name' => $variant->color_name,
                        'color_code' => $variant->color_code,
                        'stock_quantity' => (int) $variant->stock_quantity,
                        'price_modifier' => (float) $variant->price_modifier,
                    ];
                })->values()->all();
            }),
        ];
    }
}
