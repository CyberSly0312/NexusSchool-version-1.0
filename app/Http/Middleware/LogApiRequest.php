<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiAuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        ApiAuditLog::create([
            'api_client_id' => $request->input('api_client')?->id,
            'school_id' => $request->input('api_client')?->school_id ?? '1',
            'user_id' => $request->input('api_user')?->id,
            'api_token_id' => $request->input('api_token')?->id,
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_body' => $request->except(['password', 'secret', 'token']),
            'response_time_ms' => $duration,
        ]);

        return $response;
    }
}
