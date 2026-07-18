<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Listeners;

use App\Domains\Gradebook\Events\AssessmentResultsPublished;
use Illuminate\Support\Facades\Log;

class LogAssessmentResultsPublished
{
    public function handle(AssessmentResultsPublished $event): void
    {
        Log::info('Assessment results published', [
            'assessment_id' => $event->assessmentId,
            'result_count' => count($event->resultIds),
            'first_ids' => array_slice($event->resultIds, 0, 10),
        ]);
    }
}
