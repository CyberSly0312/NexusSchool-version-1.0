<?php

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearUpdated;
use Illuminate\Support\Facades\Log;

class LogAcademicYearUpdated
{
    public function handle(AcademicYearUpdated $event): void
    {
        Log::info('Academic year updated', [
            'academic_year_id' => $event->academicYear->id,
            'name' => $event->academicYear->name,
            'status' => $event->academicYear->status->value,
        ]);
    }
}
