<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $logData = [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'request_payload' =>json_encode(array_slice($request->all(), 0, 100)),
                'request_time' => now(),
            ];

            $apiLog = ApiLog::create($logData);

            $response = $next($request);

            $apiLog->update([
                'response_payload' => $response->getContent(),
                'status_code' => $response->getStatusCode(),
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Logging API request failed: '.$e->getMessage());
            return response()->json(['message' => 'Logging failed'], 500);
        }
    }
}
