<?php

namespace App\Http\Controllers\Api;

abstract class Controller
{
    protected function successResponse($data = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message = 'Error', $code = 400, $error = null, $errorCode = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => [],
            'error_code' => $errorCode,
        ];

        if ($error && ! app()->environment('production')) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }
}
