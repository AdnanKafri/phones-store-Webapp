<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function register(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('mobile_app')->plainTextToken;

            return [
                'user' => $user->fresh(),
                'token' => $token,
            ];
        });
    }
}
