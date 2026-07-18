<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Listeners;

use App\Domains\Gradebook\Events\GradebookCalculated;
use Illuminate\Support\Facades\Log;

class LogGradebookCalculated
{
    public function handle(GradebookCalculated $event): void
    {
        Log::info('Gradebook calculated', [
            'student_id' => $event->studentId,
            'term_id' => $event->academicTermId,
            'year_id' => $event->academicYearId,
            'term_average' => $event->termAverage,
            'weighted_average' => $event->weightedAverage,
        ]);
    }
}
