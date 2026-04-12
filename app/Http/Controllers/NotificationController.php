<?php

namespace App\Http\Controllers;

use App\Services\Notifications\UserNotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private UserNotificationService $userNotificationService,
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $notifications = $this->userNotificationService->getUserNotifications($user);
        $this->userNotificationService->markAllAsRead($user);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = $this->userNotificationService->markAsRead(Auth::user(), $id);

        if (! $notification) {
            abort(404);
        }

        return back();
    }
}
