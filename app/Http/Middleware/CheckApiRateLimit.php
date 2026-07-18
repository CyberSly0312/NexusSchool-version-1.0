<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckApiRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->input('api_client');
        if (! $client) {
            return $next($request);
        }

        $key = "api_rate:{$client->id}:".now()->minute;
        $minuteCount = (int) Cache::increment($key);

        if ($minuteCount === 1) {
            Cache::put($key, 1, 60);
        }

        if ($minuteCount > $client->rate_limit_per_minute) {
            return response()->json([
                'message' => 'Rate limit exceeded.',
                'retry_after' => 60 - now()->second,
            ], 429)->header('X-RateLimit-Limit', (string) $client->rate_limit_per_minute)
                ->header('X-RateLimit-Remaining', '0')
                ->header('Retry-After', (string) (60 - now()->second));
        }

        $dailyKey = "api_rate_daily:{$client->id}:".now()->format('Y-m-d');
        $dailyCount = (int) Cache::increment($dailyKey);

        if ($dailyCount === 1) {
            Cache::put($dailyKey, 1, now()->endOfDay());
        }

        if ($dailyCount > $client->rate_limit_per_day) {
            return response()->json([
                'message' => 'Daily rate limit exceeded.',
                'retry_after' => now()->endOfDay()->diffInSeconds(),
            ], 429)->header('X-RateLimit-Limit', (string) $client->rate_limit_per_day)
                ->header('X-RateLimit-Remaining', '0')
                ->header('Retry-After', (string) now()->endOfDay()->diffInSeconds());
        }

        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', (string) $client->rate_limit_per_minute);
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, $client->rate_limit_per_minute - $minuteCount));
        $response->headers->set('X-RateLimit-Daily-Remaining', (string) max(0, $client->rate_limit_per_day - $dailyCount));

        return $response;
    }
}
