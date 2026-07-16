<?php

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearSwitched;
use Illuminate\Support\Facades\Log;

class LogAcademicYearSwitched
{
    public function handle(AcademicYearSwitched $event): void
    {
        Log::info('Academic year switched for school', [
            'school_id' => $event->school->id,
            'previous_year_id' => $event->previousYear?->id,
            'new_year_id' => $event->newYear->id,
            'new_year_name' => $event->newYear->name,
        ]);
    }
}
