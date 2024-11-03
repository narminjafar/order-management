<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$idParam): Response
    {
        $id = $request->route($idParam);

        if (!is_numeric($id) || intval($id) != $id) {
            return response()->json(['message' => 'ID must be an integer.'], 404);
        }

        return $next($request);
    }
}
