<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponser
{
    /**
     * Generate a success response in JSON format.
     *
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    private function successResponse(mixed $data, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => 'true',
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
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }

    /**
     * Generate a no content response
     *
     * @return JsonResponse
     */
    private function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    
}
