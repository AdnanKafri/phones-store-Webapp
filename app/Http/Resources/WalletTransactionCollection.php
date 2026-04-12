<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletTransactionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(fn ($transaction) => (new WalletTransactionResource($transaction))->resolve($request))
            ->values()
            ->all();
    }

    public function meta(): ?array
    {
        if (! $this->resource instanceof LengthAwarePaginator) {
            return null;
        }

        return [
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'per_page' => $this->resource->perPage(),
            'total' => $this->resource->total(),
            'from' => $this->resource->firstItem(),
            'to' => $this->resource->lastItem(),
            'has_more_pages' => $this->resource->hasMorePages(),
        ];
    }
}
