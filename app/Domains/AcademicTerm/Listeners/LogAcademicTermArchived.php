<?php

namespace App\Domains\AcademicTerm\Listeners;

use App\Domains\AcademicTerm\Events\AcademicTermArchived;
use Illuminate\Support\Facades\Log;

class LogAcademicTermArchived
{
    public function handle(AcademicTermArchived $event): void
    {
        Log::info('Academic term archived', [
            'academic_term_id' => $event->academicTerm->id,
            'name' => $event->academicTerm->name,
            'academic_year_id' => $event->academicTerm->academic_year_id,
        ]);
    }
}
