<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RetryWebhookDelivery implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 30;

    public function __construct(
        public readonly string $deliveryId,
    ) {
    }

    public function handle(): void
    {
        $delivery = WebhookDelivery::findOrFail($this->deliveryId);
        $webhook = $delivery->webhook;

        if (! $webhook || ! $webhook->is_active) {
            return;
        }

        if ($delivery->retry_count >= $delivery->max_retries) {
            return;
        }

        $delivery->increment('retry_count');

        $signature = hash_hmac('sha256', json_encode($delivery->payload), $webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-Event' => $delivery->event,
                    'Content-Type' => 'application/json',
                ])
                ->post($webhook->url, $delivery->payload);

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'success' => $response->successful(),
                'delivered_at' => $response->successful() ? now() : null,
            ]);
        } catch (\Throwable $e) {
            $delivery->update([
                'response_body' => $e->getMessage(),
                'success' => false,
                'next_retry_at' => now()->addMinutes(5 * $delivery->retry_count),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Webhook retry delivery failed', [
            'delivery_id' => $this->deliveryId,
            'error' => $exception->getMessage(),
        ]);
    }
}
