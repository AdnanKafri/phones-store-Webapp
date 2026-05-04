<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\DeviceRequestCollection;
use App\Http\Resources\DeviceRequestResource;
use App\Models\DeviceRequest;
use Illuminate\Http\Request;

class DeviceRequestController extends ApiController
{
    public function index(Request $request)
    {
        $requests = DeviceRequest::where('status', 'approved')
            ->with('user')
            ->latest()
            ->paginate(15);

        return $this->resourceResponse(
            new DeviceRequestCollection($requests),
            'Device requests retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $deviceRequest = DeviceRequest::create([
            'user_id' => $request->user()->id,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        \App\Services\NotificationService::broadcastToAdmins(
            'New Device Request', 
            "User " . $request->user()->name . " is looking for {$validated['brand']} {$validated['model']}."
        );

        $deviceRequest->load('user');

        return $this->resourceResponse(
            new DeviceRequestResource($deviceRequest),
            'Your request is being reviewed! We will notify you when approved.',
            201
        );
    }
    
    public function offer(Request $request, DeviceRequest $deviceRequest)
    {
        if ($deviceRequest->user_id === $request->user()->id) {
            return $this->errorResponse('You cannot offer on your own request.', 'FORBIDDEN', 403);
        }

        $offerer = $request->user();

        // Check for duplicate offers
        $existingOffer = $deviceRequest->user->notifications()
            ->where('data->type', 'device_offer')
            ->where('data->offerer_id', $offerer->id)
            ->where('data->device_request_id', $deviceRequest->id)
            ->first();

        if ($existingOffer) {
            return $this->errorResponse('You have already sent an offer for this request.', 'ALREADY_OFFERED', 422);
        }
        
        $deviceRequest->user->notify(new \App\Notifications\OfferDeviceNotification(
            $offerer, 
            "{$deviceRequest->brand} {$deviceRequest->model}",
            $deviceRequest->id
        ));

        return $this->successResponse(null, 'Offer sent successfully to the requester.');
    }
}
