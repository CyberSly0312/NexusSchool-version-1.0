<?php

declare(strict_types=1);

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Events\MessageSent;
use App\Domains\Communication\Events\NotificationQueued;
use App\Domains\Communication\Events\NotificationSent;
use App\Jobs\SendNotification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class QueueDispatcher
{
    use Dispatchable;
    use InteractsWithQueue;

    public function handleNotificationQueued(NotificationQueued $event): void
    {
        SendNotification::dispatch($event->notification->id);
    }

    public function handleNotificationSent(NotificationSent $event): void
    {
        // Delivery tracking is handled by DeliveryTracker listener
    }

    public function handleMessageSent(MessageSent $event): void
    {
        Log::info('MessageSent queued for delivery', [
            'message_id' => $event->message->id,
            'school_id' => $event->message->school_id,
        ]);
    }
}
