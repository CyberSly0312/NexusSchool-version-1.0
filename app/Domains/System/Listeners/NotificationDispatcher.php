<?php

declare(strict_types=1);

namespace App\Domains\System\Listeners;

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
        $entity = $this->extractEntity($event);
        $eventName = class_basename($event);

        Log::info("System Notification: {$eventName}", [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id ?? null,
            'school_id' => $entity->school_id ?? null,
        ]);
    }
}
