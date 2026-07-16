<?php

namespace App\Domains\AcademicClass\Listeners;

use App\Domains\AcademicClass\Events\AcademicClassArchived;
use Illuminate\Support\Facades\Log;

class LogAcademicClassArchived
{
    public function handle(AcademicClassArchived $event): void
    {
        Log::info('Academic class archived', [
            'academic_class_id' => $event->academicClass->id,
            'name' => $event->academicClass->name,
            'academic_year_id' => $event->academicClass->academic_year_id,
        ]);
    }
}
