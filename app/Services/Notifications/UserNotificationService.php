<?php

namespace App\Services\Notifications;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class UserNotificationService
{
    public function getUserNotifications($user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->latest()->paginate($perPage);
    }

    public function markAllAsRead($user): int
    {
        return $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function markAsRead($user, string $notificationId): ?DatabaseNotification
    {
        $notification = $user->notifications()->find($notificationId);

        if (! $notification) {
            return null;
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return $notification;
    }
}
