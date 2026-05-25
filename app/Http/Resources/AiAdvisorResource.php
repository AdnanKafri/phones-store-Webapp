<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiAdvisorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'filters' => $this->resource['filters'],
            'products' => ProductResource::collection($this->resource['products'])->resolve($request),
        ];
    }
}
