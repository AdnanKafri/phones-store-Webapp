<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComparisonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'devices' => $this->resource['devices'],
            'rows' => $this->resource['rows'],
        ];
    }
}
