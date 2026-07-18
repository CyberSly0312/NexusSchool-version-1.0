<?php

declare(strict_types=1);

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Events\NotificationDelivered;
use App\Domains\Communication\Events\NotificationFailed;
use App\Domains\Communication\Events\NotificationSent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DeliveryTracker
{
    use Dispatchable;
    use InteractsWithQueue;

    public function handleNotificationSent(NotificationSent $event): void
    {
        Log::info('Delivery tracking started', [
            'notification_id' => $event->notification->id,
            'deliveries_count' => $event->notification->deliveries()->count(),
        ]);
    }

    public function handleNotificationDelivered(NotificationDelivered $event): void
    {
        $notification = $event->notification;
        $totalDeliveries = $notification->deliveries()->count();
        $deliveredCount = $notification->deliveries()
            ->where('status', 'delivered')
            ->count();

        Log::info('Delivery progress updated', [
            'notification_id' => $notification->id,
            'delivered' => $deliveredCount,
            'total' => $totalDeliveries,
        ]);
    }

    public function handleNotificationFailed(NotificationFailed $event): void
    {
        Log::warning('Notification delivery failed', [
            'notification_id' => $event->notification->id,
            'retry_count' => $event->notification->retry_count,
            'last_error' => $event->notification->last_error,
        ]);
    }
}
