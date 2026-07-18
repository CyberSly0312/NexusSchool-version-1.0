<?php

declare(strict_types=1);

namespace App\Domains\AcademicClass\Listeners;

use App\Domains\AcademicClass\Events\AcademicClassActivated;
use Illuminate\Support\Facades\Log;

class LogAcademicClassActivated
{
    public function handle(AcademicClassActivated $event): void
    {
        Log::info('Academic class activated', [
            'academic_class_id' => $event->academicClass->id,
            'name' => $event->academicClass->name,
            'academic_year_id' => $event->academicClass->academic_year_id,
        ]);
    }
}
