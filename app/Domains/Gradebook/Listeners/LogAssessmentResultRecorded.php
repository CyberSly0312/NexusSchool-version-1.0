<?php

namespace App\Domains\Gradebook\Listeners;

use App\Domains\Gradebook\Events\AssessmentResultRecorded;
use Illuminate\Support\Facades\Log;

class LogAssessmentResultRecorded
{
    public function handle(AssessmentResultRecorded $event): void
    {
        Log::info('Assessment result recorded', [
            'result_id' => $event->result->id,
            'student_id' => $event->result->student_id,
            'assessment_id' => $event->result->assessment_id,
            'school_id' => $event->result->school_id,
            'raw_score' => $event->result->raw_score,
        ]);
    }
}
