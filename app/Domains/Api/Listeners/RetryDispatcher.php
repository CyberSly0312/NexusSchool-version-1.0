<?php

declare(strict_types=1);

namespace App\Domains\Api\Listeners;

use App\Domains\Api\Events\WebhookFailed;
use App\Jobs\RetryWebhookDelivery;

class RetryDispatcher
{
    public function handleWebhookFailed(WebhookFailed $event): void
    {
        $webhook = $event->webhook;
        $delivery = $event->delivery;

        if (! $webhook->is_active) {
            return;
        }

        if ($delivery->retry_count >= $delivery->max_retries) {
            return;
        }

        RetryWebhookDelivery::dispatch($delivery->id);
    }
}
