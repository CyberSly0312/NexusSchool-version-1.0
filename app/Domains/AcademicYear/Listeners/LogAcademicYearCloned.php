<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Listeners;

use App\Domains\AcademicYear\Events\AcademicYearCloned;
use Illuminate\Support\Facades\Log;

class LogAcademicYearCloned
{
    public function handle(AcademicYearCloned $event): void
    {
        Log::info('Academic year cloned', [
            'original_id' => $event->original->id,
            'clone_id' => $event->clone->id,
            'original_name' => $event->original->name,
            'clone_name' => $event->clone->name,
        ]);
    }
}
