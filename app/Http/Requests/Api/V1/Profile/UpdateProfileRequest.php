<?php

namespace App\Http\Requests\Api\V1\Profile;

use App\Http\Requests\ProfileUpdateRequest as WebProfileUpdateRequest;

class UpdateProfileRequest extends WebProfileUpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
