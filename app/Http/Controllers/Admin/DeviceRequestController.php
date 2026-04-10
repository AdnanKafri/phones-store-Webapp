<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceRequest;
use Illuminate\Http\Request;

class DeviceRequestController extends Controller
{
    public function index()
    {
        $requests = DeviceRequest::with('user')->latest()->paginate(20);
        return view('admin.device_requests.index', compact('requests'));
    }

    public function update(Request $request, DeviceRequest $deviceRequest)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        
        $deviceRequest->update(['status' => $request->status]);
        
        if ($request->status === 'approved') {
            \App\Services\NotificationService::send($deviceRequest->user_id, 'طلبك تمت الموافقة عليه!', "تم نشر طلبك لجهاز {$deviceRequest->brand} {$deviceRequest->model} في الصفحة الرئيسية.", 'system');
        }

        return back()->with('success', 'Request status updated.');
    }

    public function destroy(DeviceRequest $deviceRequest)
    {
        $deviceRequest->delete();
        return back()->with('success', 'Request deleted.');
    }
}
