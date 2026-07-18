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
use Illuminate\Support\Facades\Log;

class RetryFailedNotification implements ShouldQueue
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

        if (! $notification->canRetry()) {
            return;
        }

        $notification->update(['status' => NotificationStatus::Queued->value]);

        // Reset delivery statuses to pending for retry
        $notification->deliveries()
            ->where('status', 'failed')
            ->update(['status' => 'pending', 'error_message' => null, 'failed_at' => null]);

        SendNotification::dispatch($notification->id);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('RetryFailedNotification failed', [
            'notification_id' => $this->notificationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
