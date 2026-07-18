<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearDeleted;
use Illuminate\Support\Facades\Log;

class LogAcademicYearDeleted
{
    public function handle(AcademicYearDeleted $event): void
    {
        Log::info('Academic year deleted', [
            'academic_year_id' => $event->academicYear->id,
            'name' => $event->academicYear->name,
            'school_id' => $event->academicYear->school_id,
        ]);
    }
}
