<?php

namespace App\Domains\AcademicTerm\Listeners;

use App\Domains\AcademicTerm\Events\AcademicTermActivated;
use Illuminate\Support\Facades\Log;

class LogAcademicTermActivated
{
    public function handle(AcademicTermActivated $event): void
    {
        Log::info('Academic term activated', [
            'academic_term_id' => $event->academicTerm->id,
            'name' => $event->academicTerm->name,
            'academic_year_id' => $event->academicTerm->academic_year_id,
        ]);
    }
}
