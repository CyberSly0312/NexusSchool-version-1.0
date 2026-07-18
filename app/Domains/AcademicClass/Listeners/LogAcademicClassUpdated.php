<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Listeners;

use App\Domains\AcademicClass\Events\AcademicClassUpdated;
use Illuminate\Support\Facades\Log;

class LogAcademicClassUpdated
{
    public function handle(AcademicClassUpdated $event): void
    {
        Log::info('Academic class updated', [
            'academic_class_id' => $event->academicClass->id,
            'name' => $event->academicClass->name,
            'status' => $event->academicClass->status->value,
        ]);
    }
}
