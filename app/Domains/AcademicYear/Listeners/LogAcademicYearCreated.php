<?php

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearCreated;
use Illuminate\Support\Facades\Log;

class LogAcademicYearCreated
{
    public function handle(AcademicYearCreated $event): void
    {
        Log::info('Academic year created', [
            'academic_year_id' => $event->academicYear->id,
            'name' => $event->academicYear->name,
            'school_id' => $event->academicYear->school_id,
        ]);
    }
}
