<?php

declare(strict_types=1);

namespace App\Domains\User;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Owning provider for the User domain's public surfaces.
 *
 * The named limiter protects the tenant-host-bound invitation activation
 * endpoint. Tokens are reduced to digests before being used as cache keys, and
 * the request IP is part of the strategy.
 */
final class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('invitation-activation', function (Request $request): array {
            $tokenDigest = hash('sha256', (string) $request->route('token'));

            return [
                Limit::perMinute(60)->by('ip:'.(string) $request->ip()),
                Limit::perMinute(10)->by('token:'.$tokenDigest),
            ];
        });
    }
}
