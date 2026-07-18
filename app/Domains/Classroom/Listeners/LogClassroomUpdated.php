<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Listeners;

use App\Domains\Classroom\Events\ClassroomUpdated;
use Illuminate\Support\Facades\Log;

class LogClassroomUpdated
{
    public function handle(ClassroomUpdated $event): void
    {
        Log::info('Classroom updated', [
            'classroom_id' => $event->classroom->id,
            'name' => $event->classroom->name,
            'status' => $event->classroom->status->value,
        ]);
    }
}
