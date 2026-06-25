<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiAdvisorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $payload = [
            'filters' => $this->resource['filters'],
            'products' => ProductResource::collection($this->resource['products'])->resolve($request),
        ];

        if (isset($this->resource['search_meta'])) {
            $payload['search_meta'] = $this->resource['search_meta'];
        }

        return $payload;
    }
}
