<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearArchived;
use Illuminate\Support\Facades\Log;

class LogAcademicYearArchived
{
    public function handle(AcademicYearArchived $event): void
    {
        Log::info('Academic year archived', [
            'academic_year_id' => $event->academicYear->id,
            'name' => $event->academicYear->name,
            'school_id' => $event->academicYear->school_id,
        ]);
    }
}
