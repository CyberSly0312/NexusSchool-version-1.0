<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Listeners;

use App\Domains\Gradebook\Events\AssessmentResultVerified;
use Illuminate\Support\Facades\Log;

class LogAssessmentResultVerified
{
    public function handle(AssessmentResultVerified $event): void
    {
        Log::info('Assessment result verified', [
            'result_id' => $event->result->id,
            'student_id' => $event->result->student_id,
            'assessment_id' => $event->result->assessment_id,
            'school_id' => $event->result->school_id,
        ]);
    }
}
