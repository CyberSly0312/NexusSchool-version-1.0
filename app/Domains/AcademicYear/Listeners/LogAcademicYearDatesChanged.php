<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearDatesChanged;
use Illuminate\Support\Facades\Log;

class LogAcademicYearDatesChanged
{
    public function handle(AcademicYearDatesChanged $event): void
    {
        Log::info('Academic year dates changed', [
            'academic_year_id' => $event->academicYear->id,
            'old_start_date' => $event->oldStartDate,
            'old_end_date' => $event->oldEndDate,
            'new_start_date' => $event->academicYear->start_date?->toDateString(),
            'new_end_date' => $event->academicYear->end_date?->toDateString(),
        ]);
    }
}
