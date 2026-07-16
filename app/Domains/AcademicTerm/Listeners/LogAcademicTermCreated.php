<?php

namespace App\Domains\AcademicTerm\Listeners;

use App\Domains\AcademicTerm\Events\AcademicTermCreated;
use Illuminate\Support\Facades\Log;

class LogAcademicTermCreated
{
    public function handle(AcademicTermCreated $event): void
    {
        Log::info('Academic term created', [
            'academic_term_id' => $event->academicTerm->id,
            'name' => $event->academicTerm->name,
            'academic_year_id' => $event->academicTerm->academic_year_id,
        ]);
    }
}
