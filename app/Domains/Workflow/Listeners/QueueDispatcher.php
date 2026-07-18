<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Listeners;

use App\Domains\Shared\Traits\HasEntityExtraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class QueueDispatcher
{
    use Dispatchable;
    use HasEntityExtraction;
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $entity = $this->extractEntity($event);
        $eventName = class_basename($event);

        Log::info("Workflow Queue Dispatch: {$eventName}", [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id ?? null,
        ]);
    }
}
