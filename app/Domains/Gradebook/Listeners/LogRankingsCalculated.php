<?php

namespace App\Domains\Gradebook\Listeners;

use App\Domains\Gradebook\Events\RankingsCalculated;
use Illuminate\Support\Facades\Log;

class LogRankingsCalculated
{
    public function handle(RankingsCalculated $event): void
    {
        Log::info('Assessment rankings calculated', [
            'assessment_id' => $event->assessmentId,
            'student_count' => $event->studentCount,
        ]);
    }
}
