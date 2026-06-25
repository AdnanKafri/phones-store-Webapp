<?php

namespace App\Http\Controllers\Api\V1\Devices;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\DeviceCollection;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Services\Devices\DeviceCatalogService;
use Illuminate\Http\Request;

class DeviceController extends ApiController
{
    public function __construct(
        private DeviceCatalogService $deviceCatalogService,
    ) {
    }

    public function index(Request $request)
    {
        $devices = $this->deviceCatalogService->getPublicDevices(
            $request->only(['brand', 'q']),
            (int) $request->integer('per_page', 15),
        );

        return $this->resourceResponse(
            new DeviceCollection($devices),
            'Devices retrieved successfully.'
        );
    }

    public function show(Device $device)
    {
        $device = $this->deviceCatalogService->loadDevice($device);

        return $this->resourceResponse(
            new DeviceResource($device),
            'Device retrieved successfully.'
        );
    }
}
