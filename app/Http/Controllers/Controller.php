<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function success($data, $statusCode, $message='success') {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data['data'] ?? $data,
            'status_code' => $statusCode
        ], $statusCode);
    }

    protected function failedResponse($message, $statusCode) {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            'status_code' => $statusCode
        ], $statusCode);
    }
}
