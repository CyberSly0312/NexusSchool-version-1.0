<?php

declare(strict_types=1);

namespace App\Domains\AcademicTerm\Listeners;

use App\Domains\AcademicTerm\Events\AcademicTermUpdated;
use Illuminate\Support\Facades\Log;

class LogAcademicTermUpdated
{
    public function handle(AcademicTermUpdated $event): void
    {
        Log::info('Academic term updated', [
            'academic_term_id' => $event->academicTerm->id,
            'name' => $event->academicTerm->name,
            'status' => $event->academicTerm->status->value,
        ]);
    }
}
