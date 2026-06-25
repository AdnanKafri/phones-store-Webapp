<?php

namespace App\Services\Devices;

use App\Models\Device;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DeviceCatalogService
{
    public function getPublicDevices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Device::query()
            ->withCount('products')
            ->where('is_active', true);

        if (! empty($filters['brand'])) {
            $query->where('brand', 'like', '%'.$filters['brand'].'%');
        }

        if (! empty($filters['q'])) {
            $term = $filters['q'];

            $query->where(function (Builder $builder) use ($term) {
                $builder
                    ->where('brand', 'like', '%'.$term.'%')
                    ->orWhere('model_name', 'like', '%'.$term.'%')
                    ->orWhere('processor', 'like', '%'.$term.'%')
                    ->orWhere('display', 'like', '%'.$term.'%');
            });
        }

        return $query
            ->orderBy('brand')
            ->orderBy('model_name')
            ->paginate($perPage);
    }

    public function getSelectableDevices(int $limit = 100): Collection
    {
        return Device::query()
            ->where('is_active', true)
            ->orderBy('brand')
            ->orderBy('model_name')
            ->limit($limit)
            ->get();
    }

    public function loadDevice(Device $device): Device
    {
        return $device->loadCount('products');
    }

    public function resolveProductDevice(Product $product): ?Device
    {
        if ($product->relationLoaded('device') && $product->device) {
            return $product->device;
        }

        if ($product->device_id) {
            return $product->device()->first();
        }

        $normalizedProduct = Str::lower(trim($product->brand.' '.$product->model));

        return Device::query()
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($product, $normalizedProduct) {
                $builder
                    ->where(function (Builder $brandModelQuery) use ($product) {
                        $brandModelQuery
                            ->where('brand', 'like', '%'.$product->brand.'%')
                            ->where('model_name', 'like', '%'.$product->model.'%');
                    })
                    ->orWhereRaw(
                        'LOWER(CONCAT(brand, " ", model_name)) = ?',
                        [$normalizedProduct]
                    );
            })
            ->first();
    }
}
