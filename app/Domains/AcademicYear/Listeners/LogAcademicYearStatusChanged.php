<?php

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearStatusChanged;
use Illuminate\Support\Facades\Log;

class LogAcademicYearStatusChanged
{
    public function handle(AcademicYearStatusChanged $event): void
    {
        Log::info('Academic year status changed', [
            'academic_year_id' => $event->academicYear->id,
            'old_status' => $event->oldStatus->value,
            'new_status' => $event->newStatus->value,
        ]);
    }
}
