<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiController extends Controller
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = 200,
        ?array $meta = null,
    ): JsonResponse {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function resourceResponse(
        JsonResource $resource,
        string $message = 'Request completed successfully.',
        int $status = 200,
    ): JsonResponse {
        $meta = method_exists($resource, 'meta') ? $resource->meta() : null;

        return $this->successResponse(
            $resource->resolve(request()),
            $message,
            $status,
            $meta,
        );
    }

    protected function errorResponse(
        string $message,
        string $code = 'ERROR',
        int $status = 400,
        array $errors = [],
    ): JsonResponse {
        return ApiResponse::error($message, $code, $status, $errors);
    }
}
