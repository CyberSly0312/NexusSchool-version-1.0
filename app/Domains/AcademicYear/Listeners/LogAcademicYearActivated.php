<?php

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearActivated;
use Illuminate\Support\Facades\Log;

class LogAcademicYearActivated
{
    public function handle(AcademicYearActivated $event): void
    {
        Log::info('Academic year activated', [
            'academic_year_id' => $event->academicYear->id,
            'name' => $event->academicYear->name,
            'school_id' => $event->academicYear->school_id,
        ]);
    }
}
