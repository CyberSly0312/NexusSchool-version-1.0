<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Api\Services\WebhookService;
use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public readonly string $webhookId,
        public readonly string $event,
        public readonly array $payload,
    ) {
    }

    public function handle(WebhookService $webhookService): void
    {
        $webhook = Webhook::findOrFail($this->webhookId);

        if (! $webhook->is_active) {
            return;
        }

        $signature = hash_hmac('sha256', json_encode($this->payload), $webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-Event' => $this->event,
                    'Content-Type' => 'application/json',
                ])
                ->post($webhook->url, $this->payload);

            $webhookService->recordDelivery(
                $webhook,
                $this->event,
                $this->payload,
                $response->status(),
                $response->body(),
                $response->successful(),
            );
        } catch (\Throwable $e) {
            $webhookService->recordDelivery(
                $webhook,
                $this->event,
                $this->payload,
                null,
                $e->getMessage(),
                false,
            );

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Webhook delivery failed permanently', [
            'webhook_id' => $this->webhookId,
            'event' => $this->event,
            'error' => $exception->getMessage(),
        ]);
    }
}
