<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model_name' => $this->model_name,
            'slug' => $this->slug,
            'name' => trim($this->brand.' '.$this->model_name),
            'image_url' => $this->image_url,
            'release_year' => $this->release_year,
            'marketplace_products_count' => $this->whenCounted('products', fn () => (int) $this->products_count),
            'specifications' => [
                'battery' => $this->battery,
                'camera' => $this->camera,
                'storage' => $this->storage,
                'ram' => $this->ram,
                'processor' => $this->processor,
                'performance' => $this->performance,
                'display' => $this->display,
                'operating_system' => $this->operating_system,
            ],
        ];
    }
}
