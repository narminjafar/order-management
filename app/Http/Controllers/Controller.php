<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ErrorHandleMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{

    protected function result(string $message, $data = null, int $statusCode = 200, $resourceClass = null): JsonResponse
    {

        $response = ['message' => $message];

        if (!is_null($data)) {
            $response['data'] = $resourceClass ? new $resourceClass($data) : $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function successResponse(string $message, $data, int $statusCode = 200): JsonResponse
    {
        return $this->result($message, $data, $statusCode);
    }

    protected function handleResponse(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $this->result($e->getMessage(), null, $e->getCode());
        }
    }

}
