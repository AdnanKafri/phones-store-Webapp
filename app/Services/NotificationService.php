<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function send($userId, $title, $message, $type = 'system', $data = null)
    {
        $user = User::find($userId);
        if (!$user) return;

        $user->notify(new \App\Notifications\GenericNotification($title, $message, $type, $data));
    }

    public static function broadcastToAdmins($title, $message, $type = 'system', $data = null)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\GenericNotification($title, $message, $type, $data));
        }
    }
}
