<?php

namespace App\Http\Requests\Api\V1\Compare;

use Illuminate\Foundation\Http\FormRequest;

class StoreComparisonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_ids' => ['required', 'array', 'size:2'],
            'device_ids.*' => ['required', 'integer', 'distinct', 'exists:devices,id'],
        ];
    }
}
