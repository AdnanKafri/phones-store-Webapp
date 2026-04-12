<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = 200,
        ?array $meta = null,
    ): JsonResponse {
        $payload = [
            'data' => $data,
            'message' => $message,
        ];

        if (! is_null($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    public static function error(
        string $message,
        string $code = 'ERROR',
        int $status = 400,
        array $errors = [],
    ): JsonResponse {
        $payload = [
            'message' => $message,
            'code' => $code,
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
