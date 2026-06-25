<?php

namespace App\Http\Controllers\Api\V1\Ai;

use App\Exceptions\AiAdvisorException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Ai\AiAdvisorRequest;
use App\Http\Resources\AiAdvisorResource;
use App\Services\Ai\AiAdvisorService;

class AiAdvisorController extends ApiController
{
    public function __construct(
        private AiAdvisorService $aiAdvisorService,
    ) {
    }

    public function __invoke(AiAdvisorRequest $request)
    {
        try {
            $result = $this->aiAdvisorService->advise($request->string('query')->toString());

            return $this->resourceResponse(
                new AiAdvisorResource($result),
                $result['message'] ?? 'AI recommendations generated successfully.'
            );
        } catch (AiAdvisorException $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->errorCode(),
                $exception->status(),
                $exception->errors(),
            );
        }
    }
}
