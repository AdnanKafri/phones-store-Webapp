<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Services\Notifications\UserNotificationService;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function __construct(
        private UserNotificationService $userNotificationService,
    ) {
    }

    public function index(Request $request)
    {
        $notifications = $this->userNotificationService->getUserNotifications($request->user());

        return $this->resourceResponse(
            new NotificationCollection($notifications),
            'Notifications retrieved successfully.'
        );
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $this->userNotificationService->markAsRead($request->user(), $id);

        if (! $notification) {
            return $this->errorResponse(
                'Notification not found.',
                'NOTIFICATION_NOT_FOUND',
                404
            );
        }

        return $this->resourceResponse(
            new NotificationResource($notification),
            'Notification marked as read successfully.'
        );
    }

    public function markAllAsRead(Request $request)
    {
        $count = $this->userNotificationService->markAllAsRead($request->user());

        return $this->successResponse(
            ['updated_count' => $count],
            'Notifications marked as read successfully.'
        );
    }
}
