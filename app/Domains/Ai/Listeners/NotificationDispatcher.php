<?php

declare(strict_types=1);

namespace App\Domains\Ai\Listeners;

use App\Domains\Shared\Traits\HasEntityExtraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    use Dispatchable;
    use HasEntityExtraction;
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $eventName = class_basename($event);

        if ($event instanceof \App\Domains\Ai\Events\UsageQuotaExceeded) {
            Log::warning("AI Quota Exceeded: {$event->quotaType}", [
                'school_id' => $event->schoolId,
                'quota_type' => $event->quotaType,
                'current_usage' => $event->currentUsage,
                'limit' => $event->limit,
            ]);

            return;
        }

        $entity = $this->extractEntity($event);

        Log::info("AI Notification: {$eventName}", [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id ?? null,
            'school_id' => $entity->school_id ?? null,
        ]);
    }
}
