<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ApiClientController;
use App\Http\Controllers\Api\V1\ApiTokenController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\OAuthController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Middleware\CheckApiRateLimit;
use App\Http\Middleware\LogApiRequest;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public OAuth routes (rate limited)
    Route::post('/oauth/token', [OAuthController::class, 'token'])
        ->middleware('throttle:10,1');

    // Protected API routes
    Route::middleware([ValidateApiToken::class, CheckApiRateLimit::class, LogApiRequest::class])->group(function () {
        Route::get('/oauth/me', [OAuthController::class, 'me']);
        Route::post('/oauth/revoke', [OAuthController::class, 'revoke']);

        // API Clients
        Route::apiResource('clients', ApiClientController::class);
        Route::post('/clients/{id}/disable', [ApiClientController::class, 'disable']);
        Route::post('/clients/{id}/enable', [ApiClientController::class, 'enable']);
        Route::post('/clients/{id}/regenerate-secret', [ApiClientController::class, 'regenerateSecret']);

        // API Tokens (nested under clients)
        Route::apiResource('clients/{clientId}/tokens', ApiTokenController::class);
        Route::post('/clients/{clientId}/tokens/{tokenId}/revoke', [ApiTokenController::class, 'revoke']);

        // Webhooks (nested under clients)
        Route::apiResource('clients/{clientId}/webhooks', WebhookController::class);
        Route::post('/clients/{clientId}/webhooks/{webhookId}/disable', [WebhookController::class, 'disable']);
        Route::post('/clients/{clientId}/webhooks/{webhookId}/enable', [WebhookController::class, 'enable']);
        Route::get('/clients/{clientId}/webhooks/{webhookId}/deliveries', [WebhookController::class, 'deliveries']);

        // Audit Logs (read-only)
        Route::get('/audit-logs', [AuditLogController::class, 'index']);
        Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
    });
});
