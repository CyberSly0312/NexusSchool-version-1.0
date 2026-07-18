<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Listeners;

use App\Domains\Classroom\Events\ClassroomCreated;
use Illuminate\Support\Facades\Log;

class LogClassroomCreated
{
    public function handle(ClassroomCreated $event): void
    {
        Log::info('Classroom created', [
            'classroom_id' => $event->classroom->id,
            'name' => $event->classroom->name,
            'code' => $event->classroom->code,
            'school_id' => $event->classroom->school_id,
        ]);
    }
}
