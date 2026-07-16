<?php

namespace App\Domains\SubjectCoefficient\Listeners;

use App\Domains\SubjectCoefficient\Events\SubjectCoefficientCreated;
use Illuminate\Support\Facades\Log;

class LogSubjectCoefficientCreated
{
    public function handle(SubjectCoefficientCreated $event): void
    {
        Log::info('Subject coefficient created', [
            'subject_coefficient_id' => $event->subjectCoefficient->id,
            'subject_id' => $event->subjectCoefficient->subject_id,
            'program_id' => $event->subjectCoefficient->program_id,
            'grade_level_id' => $event->subjectCoefficient->grade_level_id,
            'coefficient' => $event->subjectCoefficient->coefficient,
            'school_id' => $event->subjectCoefficient->school_id,
        ]);
    }
}
