<?php

namespace App\Domains\AcademicClass\Listeners;

use App\Domains\AcademicClass\Events\AcademicClassCreated;
use Illuminate\Support\Facades\Log;

class LogAcademicClassCreated
{
    public function handle(AcademicClassCreated $event): void
    {
        Log::info('Academic class created', [
            'academic_class_id' => $event->academicClass->id,
            'name' => $event->academicClass->name,
            'code' => $event->academicClass->code,
            'academic_year_id' => $event->academicClass->academic_year_id,
        ]);
    }
}
