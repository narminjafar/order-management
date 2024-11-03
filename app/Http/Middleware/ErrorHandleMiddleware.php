<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {

        try {
            return $next($request);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Resource not found'], 404);

        } catch (\TypeError $e) {
            return response()->json(['message' => 'Invalid parameter type'], 400);

        } catch (\Exception $e) {

            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

}
