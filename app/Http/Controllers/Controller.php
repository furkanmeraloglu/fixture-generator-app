<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * @param array|object $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function getSuccessfulResponse(array|object $data, string $message = "Successful", int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * @param string $message
     * @param int $code
     * @param array|object|null $errors
     * @return JsonResponse
     */
    public function getErrorResponse(string $message = "Something went wrong.", int $code = Response::HTTP_BAD_REQUEST, array|object $errors = null): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => false,
            'message' => $message,
            'trace' => $errors
        ]);
    }
}
