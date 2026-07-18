<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Communication\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public string $notificationId,
    ) {
    }

    public function handle(): void
    {
        $notification = Notification::findOrFail($this->notificationId);

        if ($notification->status !== NotificationStatus::Queued) {
            return;
        }

        $deliveries = $notification->deliveries()->where('status', 'pending')->get();

        foreach ($deliveries as $delivery) {
            $this->processDelivery($delivery);
        }

        $notification->update(['status' => NotificationStatus::Sent->value, 'sent_at' => now()]);
    }

    private function processDelivery($delivery): void
    {
        try {
            $delivery->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // In-app delivery is instant
            if ($delivery->channel->value === 'in_app') {
                $delivery->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }

            // Email/SMS/WhatsApp/Push would integrate with providers here
        } catch (\Exception $e) {
            $delivery->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $notification = Notification::find($this->notificationId);

        if ($notification) {
            $notification->update([
                'status' => NotificationStatus::Failed->value,
                'last_error' => $exception->getMessage(),
                'retry_count' => $notification->retry_count + 1,
            ]);
        }
    }
}
