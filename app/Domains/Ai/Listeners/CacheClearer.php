<?php

declare(strict_types=1);

namespace App\Domains\Ai\Listeners;

use App\Domains\Shared\Traits\HasEntityExtraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CacheClearer
{
    use Dispatchable;
    use HasEntityExtraction;
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $entity = $this->extractEntity($event);
        $schoolId = $entity->school_id ?? null;

        if ($schoolId) {
            Cache::tags(["ai:{$schoolId}"])->flush();
        }
    }
}
