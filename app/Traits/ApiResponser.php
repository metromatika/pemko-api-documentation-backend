<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Generate a success response in JSON format.
     *
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    private function successResponse(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => 'OK',
            'data' => $data
        ], $code);
    }

    /**
     * Generate a error response in JSON format.
     *
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    private function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['error' => ['code' => $code, 'message' => $message]], $code);
    }
}
