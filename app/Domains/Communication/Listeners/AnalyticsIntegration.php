<?php

declare(strict_types=1);

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Events\NotificationDelivered;
use App\Domains\Communication\Events\NotificationFailed;
use App\Domains\Communication\Events\NotificationSent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnalyticsIntegration
{
    use Dispatchable;
    use InteractsWithQueue;

    public function handleNotificationSent(NotificationSent $event): void
    {
        // Future analytics integration point
        // Track: notification sent count, channel distribution, time-to-send
        Log::info('Analytics: notification_sent', [
            'notification_id' => $event->notification->id,
            'channel' => $event->notification->channel->value,
            'type' => $event->notification->type,
        ]);
    }

    public function handleNotificationDelivered(NotificationDelivered $event): void
    {
        // Future analytics integration point
        // Track: delivery rate, time-to-deliver
        Log::info('Analytics: notification_delivered', [
            'notification_id' => $event->notification->id,
            'delivered_at' => $event->notification->delivered_at,
        ]);
    }

    public function handleNotificationFailed(NotificationFailed $event): void
    {
        // Future analytics integration point
        // Track: failure rate, error distribution
        Log::warning('Analytics: notification_failed', [
            'notification_id' => $event->notification->id,
            'error' => $event->notification->last_error,
            'retry_count' => $event->notification->retry_count,
        ]);
    }
}
