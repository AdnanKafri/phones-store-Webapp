<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Compare\StoreComparisonRequest;
use App\Http\Resources\ComparisonResource;
use App\Services\Devices\ComparisonService;

class CompareController extends ApiController
{
    public function __construct(
        private ComparisonService $comparisonService,
    ) {
    }

    public function __invoke(StoreComparisonRequest $request)
    {
        $comparison = $this->comparisonService->compareByIds($request->validated()['device_ids']);

        return $this->resourceResponse(
            new ComparisonResource($comparison),
            'Comparison generated successfully.'
        );
    }
}
