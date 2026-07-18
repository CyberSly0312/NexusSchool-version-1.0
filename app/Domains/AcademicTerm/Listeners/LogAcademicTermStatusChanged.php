<?php

declare(strict_types=1);

namespace App\Domains\AcademicTerm\Listeners;

use App\Domains\AcademicTerm\Events\AcademicTermStatusChanged;
use Illuminate\Support\Facades\Log;

class LogAcademicTermStatusChanged
{
    public function handle(AcademicTermStatusChanged $event): void
    {
        Log::info('Academic term status changed', [
            'academic_term_id' => $event->academicTerm->id,
            'old_status' => $event->oldStatus->value,
            'new_status' => $event->newStatus->value,
        ]);
    }
}
