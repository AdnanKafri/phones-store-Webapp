<?php

namespace App\Http\Requests\Api\V1\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'shipping_address' => 'required|string|max:1000',
            'color' => 'nullable|exists:product_variants,id',
            'payment_method' => 'required|in:wallet,stripe,cod',
        ];
    }
}
