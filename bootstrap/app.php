<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/verification.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Forwarded headers are honored only when the immediate sender is an
         * explicitly configured reverse proxy. An empty TRUSTED_PROXIES value
         * leaves every proxy untrusted, so forged X-Forwarded-Host/Proto/Port
         * headers cannot affect tenant resolution.
         */
        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', '')),
        )));

        if ($trustedProxies !== []) {
            $middleware->trustProxies(at: $trustedProxies);
        }

        // This runs after TrustProxies, so getHost() can use forwarded host
        // information only from an explicitly trusted immediate proxy.
        $middleware->append(\App\Http\Middleware\ValidateTrustedHost::class);

        $middleware->statefulApi();
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withSchedule(function ($schedule): void {
        $schedule->command('analytics:schedule-reports')->daily();
        $schedule->command('analytics:calculate-kpis')->hourly();
        $schedule->command('analytics:daily-snapshots')->dailyAt('02:00');
        $schedule->command('system:health-check')->everyFiveMinutes();
        $schedule->command('system:cleanup --days=90')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, \Throwable $e) {
            return $request->expectsJson() || $request->is('api/*');
        });
    })->create();
