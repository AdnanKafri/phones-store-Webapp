<?php

namespace App\Http\Controllers;

use App\Models\DeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceRequestController extends Controller
{
    public function create()
    {
        return view('device_requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DeviceRequest::create([
            'user_id' => Auth::id(),
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        \App\Services\NotificationService::broadcastToAdmins('New Device Request', "User " . Auth::user()->name . " is looking for {$validated['brand']} {$validated['model']}.");

        return redirect()->route('dashboard')->with('success', 'يتم الآن مراجعة طلبك! سنقوم بإشعارك عند توفر الجهاز.');
    }
    
    public function offer(\App\Models\DeviceRequest $deviceRequest)
    {
        // Prevent user from offering on their own request
        if ($deviceRequest->user_id === Auth::id()) {
            return back()->with('error', 'لا يمكنك تقديم عرض على طلبك الخاص.');
        }

        $offerer = Auth::user();

        // Check for duplicate offers (prevent spam)
        $existingOffer = $deviceRequest->user->notifications()
            ->where('data->type', 'device_offer')
            ->where('data->offerer_id', $offerer->id)
            ->where('data->device_request_id', $deviceRequest->id)
            ->first();

        if ($existingOffer) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لقد قمت بإرسال عرض لهذا الطلب مسبقاً.'
                ], 422);
            }
            return back()->with('error', 'لقد قمت بإرسال عرض لهذا الطلب مسبقاً.');
        }
        
        // Notify the request owner
        $deviceRequest->user->notify(new \App\Notifications\OfferDeviceNotification(
            $offerer, 
            "{$deviceRequest->brand} {$deviceRequest->model}",
            $deviceRequest->id
        ));

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار إلى صاحب الطلب بنجاح'
            ]);
        }

        return back()->with('success', 'شكراً لك! تم إشعار صاحب الطلب بأنك تمتلك هذا الجهاز.');
    }
}
