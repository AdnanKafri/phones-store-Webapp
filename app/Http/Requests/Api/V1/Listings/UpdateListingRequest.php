<?php

namespace App\Http\Requests\Api\V1\Listings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used',
            'color' => 'required|string|max:50',
            'status' => 'nullable|in:available,sold,hidden,pending,rejected',
            'description' => 'nullable|string|max:5000',
            'defects' => 'nullable|string|max:2000',
            'accessories' => 'nullable|string|max:2000',
            'disassembled_is' => 'nullable|boolean',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
