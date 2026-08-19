<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * Central API response envelope for the whole application.
 *
 * Success: { status: "success", message, data?, meta? }
 * Error:   { status: "error", message, errors? }
 */
class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'OK', int $code = 200, ?array $meta = null): JsonResponse
    {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $code);
    }

    public static function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function error(string $message = 'Something went wrong', int $code = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }

    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401);
    }

    public static function validation(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }
}
