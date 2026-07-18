<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
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
            return true;
        });
    })->create();
