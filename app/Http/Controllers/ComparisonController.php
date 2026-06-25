<?php

namespace App\Http\Controllers;

use App\Services\Devices\ComparisonService;
use App\Services\Devices\DeviceCatalogService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function __construct(
        private DeviceCatalogService $deviceCatalogService,
        private ComparisonService $comparisonService,
    ) {
    }

    public function index(Request $request)
    {
        $devices = $this->deviceCatalogService->getSelectableDevices();
        $leftDeviceId = $request->integer('left_device_id') ?: null;
        $rightDeviceId = $request->integer('right_device_id') ?: null;
        $comparison = null;

        if ($leftDeviceId && $rightDeviceId) {
            $comparison = $this->comparisonService->compareByIds([$leftDeviceId, $rightDeviceId]);
        }

        return view('compare.index', compact(
            'devices',
            'comparison',
            'leftDeviceId',
            'rightDeviceId',
        ));
    }
}
