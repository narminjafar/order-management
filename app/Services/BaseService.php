<?php

namespace App\Services;

use App\Http\Middleware\ErrorHandleMiddleware;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class BaseService
{
    protected function getAuthenticatedUserId()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $user ? $user->id : null;

        } catch (Exception $e) {
            return null;
        }
    }

}
