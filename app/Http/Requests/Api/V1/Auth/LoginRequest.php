<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Auth\LoginRequest as WebLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends WebLoginRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'device_name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function authenticateForApi()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::once($this->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return Auth::user();
    }
}
