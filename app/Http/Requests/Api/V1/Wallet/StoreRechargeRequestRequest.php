<?php

namespace App\Http\Requests\Api\V1\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class StoreRechargeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:syriatel_cash,mtn_cash,stripe',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
