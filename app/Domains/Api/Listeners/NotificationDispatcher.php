<?php

declare(strict_types=1);

namespace App\Domains\Api\Listeners;

use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Events\WebhookRegistered;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    public function handleWebhookRegistered(WebhookRegistered $event): void
    {
        Log::info('Admin notification: Webhook registered', [
            'webhook_id' => $event->webhook->id,
            'url' => $event->webhook->url,
            'api_client_id' => $event->webhook->api_client_id,
            'school_id' => $event->webhook->school_id,
        ]);
    }

    public function handleApiClientDisabled(ApiClientDisabled $event): void
    {
        Log::info('Admin notification: API client disabled', [
            'client_id' => $event->client->id,
            'client_name' => $event->client->name,
            'school_id' => $event->client->school_id,
        ]);
    }
}
