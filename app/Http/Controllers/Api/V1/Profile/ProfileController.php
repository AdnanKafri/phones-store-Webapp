<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\UserProfileResource;
use App\Services\Users\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function __construct(
        private ProfileService $profileService,
    ) {
    }

    public function show(Request $request)
    {
        return $this->resourceResponse(
            new UserProfileResource($request->user()),
            'Profile retrieved successfully.'
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return $this->resourceResponse(
            new UserProfileResource($user),
            'Profile updated successfully.'
        );
    }

    public function destroy(Request $request)
    {
        $this->profileService->deleteAccount($request->user());

        return $this->successResponse(null, 'Account deleted successfully.');
    }
}
