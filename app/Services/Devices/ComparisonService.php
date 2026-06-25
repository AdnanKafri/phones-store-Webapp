<?php

namespace App\Services\Devices;

use App\Models\Device;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ComparisonService
{
    private const SPEC_ROWS = [
        'battery' => 'Battery',
        'camera' => 'Camera',
        'storage' => 'Storage',
        'ram' => 'RAM',
        'processor' => 'Processor',
        'performance' => 'Performance',
        'display' => 'Display',
        'operating_system' => 'Operating System',
    ];

    public function __construct(
        private DeviceCatalogService $deviceCatalogService,
    ) {
    }

    public function compareByIds(array $deviceIds): array
    {
        $sanitizedIds = array_values(array_unique(array_map('intval', $deviceIds)));

        $devices = Device::query()
            ->where('is_active', true)
            ->whereIn('id', $sanitizedIds)
            ->withCount('products')
            ->get()
            ->sortBy(fn (Device $device) => array_search($device->id, $sanitizedIds, true))
            ->values();

        if ($devices->count() !== 2) {
            throw ValidationException::withMessages([
                'device_ids' => ['Exactly two valid devices are required for comparison.'],
            ]);
        }

        return $this->buildComparisonPayload($devices);
    }

    public function compareDevices(Device $leftDevice, Device $rightDevice): array
    {
        return $this->buildComparisonPayload(collect([
            $this->deviceCatalogService->loadDevice($leftDevice),
            $this->deviceCatalogService->loadDevice($rightDevice),
        ]));
    }

    private function buildComparisonPayload(Collection $devices): array
    {
        $normalizedDevices = $devices->map(function (Device $device) {
            $specifications = $this->normalizeSpecifications($device);

            return [
                'id' => $device->id,
                'brand' => $device->brand,
                'model_name' => $device->model_name,
                'slug' => $device->slug,
                'name' => trim($device->brand.' '.$device->model_name),
                'image_url' => $device->image_url,
                'release_year' => $device->release_year,
                'marketplace_products_count' => $device->products_count ?? 0,
                'specifications' => $specifications,
            ];
        })->values();

        $rows = collect(self::SPEC_ROWS)->map(function (string $label, string $key) use ($normalizedDevices) {
            $values = $normalizedDevices
                ->map(fn (array $device) => $device['specifications'][$key] ?? null)
                ->all();

            return [
                'key' => $key,
                'label' => $label,
                'values' => $values,
                'different' => count(array_unique(array_filter($values, fn ($value) => $value !== null && $value !== ''))) > 1,
            ];
        })->values()->all();

        return [
            'devices' => $normalizedDevices->all(),
            'rows' => $rows,
        ];
    }

    private function normalizeSpecifications(Device $device): array
    {
        $specs = is_array($device->specs) ? $device->specs : [];

        return [
            'battery' => $device->battery ?? $specs['battery'] ?? null,
            'camera' => $device->camera ?? $specs['camera'] ?? null,
            'storage' => $device->storage ?? $specs['storage'] ?? null,
            'ram' => $device->ram ?? $specs['ram'] ?? null,
            'processor' => $device->processor ?? $specs['processor'] ?? null,
            'performance' => $device->performance ?? $specs['performance'] ?? null,
            'display' => $device->display ?? $specs['display'] ?? null,
            'operating_system' => $device->operating_system ?? $specs['operating_system'] ?? null,
        ];
    }
}
