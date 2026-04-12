<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function updateProfile(User $user, array $validated): User
    {
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }

    public function deleteAccount(User $user): void
    {
        Auth::logout();
        $user->delete();
    }
}
