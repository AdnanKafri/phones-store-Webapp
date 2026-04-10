<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        // Mark all as read when viewing index? Or explicit action?
        // Usually mark as read.
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return back();
    }
}
