<?php

declare(strict_types=1);

namespace App\Domains\Api\Listeners;

use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Events\ApiTokenCreated;
use App\Domains\Api\Events\ApiTokenRevoked;
use App\Domains\Api\Events\WebhookDelivered;
use App\Domains\Api\Events\WebhookFailed;
use App\Domains\Api\Events\WebhookRegistered;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public function handleApiTokenCreated(ApiTokenCreated $event): void
    {
        Log::info('API Audit: Token created', [
            'token_id' => $event->token->id,
            'api_client_id' => $event->token->api_client_id,
            'school_id' => $event->token->school_id,
            'name' => $event->token->name,
            'expires_at' => $event->token->expires_at,
        ]);
    }

    public function handleApiTokenRevoked(ApiTokenRevoked $event): void
    {
        Log::info('API Audit: Token revoked', [
            'token_id' => $event->token->id,
            'api_client_id' => $event->token->api_client_id,
            'school_id' => $event->token->school_id,
        ]);
    }

    public function handleWebhookRegistered(WebhookRegistered $event): void
    {
        Log::info('API Audit: Webhook registered', [
            'webhook_id' => $event->webhook->id,
            'url' => $event->webhook->url,
            'api_client_id' => $event->webhook->api_client_id,
            'school_id' => $event->webhook->school_id,
        ]);
    }

    public function handleWebhookDelivered(WebhookDelivered $event): void
    {
        Log::info('API Audit: Webhook delivered', [
            'webhook_id' => $event->webhook->id,
            'delivery_id' => $event->delivery->id,
            'event' => $event->delivery->event,
            'response_status' => $event->delivery->response_status,
        ]);
    }

    public function handleWebhookFailed(WebhookFailed $event): void
    {
        Log::info('API Audit: Webhook failed', [
            'webhook_id' => $event->webhook->id,
            'delivery_id' => $event->delivery->id,
            'event' => $event->delivery->event,
            'response_status' => $event->delivery->response_status,
            'response_body' => $event->delivery->response_body,
        ]);
    }

    public function handleApiClientDisabled(ApiClientDisabled $event): void
    {
        Log::info('API Audit: Client disabled', [
            'client_id' => $event->client->id,
            'client_name' => $event->client->name,
            'school_id' => $event->client->school_id,
        ]);
    }
}
