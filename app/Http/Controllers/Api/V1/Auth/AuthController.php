<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function login(LoginRequest $request)
    {
        $user = $request->authenticateForApi();
        $token = $user->createToken($request->string('device_name'))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user),
        ], 'Authenticated successfully.');
    }

    public function me(Request $request)
    {
        return $this->successResponse([
            'user' => $this->userPayload($request->user()),
        ], 'Authenticated user retrieved successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }

    private function userPayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'phone' => $user->phone,
            'role' => $user->role,
            'wallet_balance' => (float) $user->wallet_balance,
            'location' => $user->location,
        ];
    }
}
