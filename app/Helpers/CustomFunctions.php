<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

if (!function_exists("validate_requests")) {
    function validate_requests($request, $rules = []): array
    {
        $validator = Validator::make($request->except(['_token']), $rules);

        if ($validator->fails()) {
            return ['passed' => false, 'message' => implode(",", $validator->messages()->all())];
        } else {
            return ['passed' => true];
        }
    }
}

if (!function_exists("response_with_status")) {
    function response_with_status($status, $message, $data = [], $headerStatus = 200): JsonResponse
    {
        try {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data //)->makeHidden($hiddenAttributes)
            ], $headerStatus);
        } catch (\Exception $e) {
            return response()->json([
                "status" => -99,
                "message" => $e->getMessage(),
            ], 500);
        }
    }
}
