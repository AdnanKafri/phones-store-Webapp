<?php

namespace App\Http\Requests\Api\V1\Listings;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
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
            'description' => 'nullable|string|max:5000',
            'condition_notes' => 'nullable|string|max:1000',
            'accessories' => 'nullable|string|max:2000',
            'disassembled_is' => 'nullable|boolean',
            'location' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
