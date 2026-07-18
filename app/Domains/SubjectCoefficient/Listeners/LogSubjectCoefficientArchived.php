<?php

declare(strict_types=1);

namespace App\Domains\SubjectCoefficient\Listeners;

use App\Domains\SubjectCoefficient\Events\SubjectCoefficientArchived;
use Illuminate\Support\Facades\Log;

class LogSubjectCoefficientArchived
{
    public function handle(SubjectCoefficientArchived $event): void
    {
        Log::info('Subject coefficient archived', [
            'subject_coefficient_id' => $event->subjectCoefficient->id,
            'subject_id' => $event->subjectCoefficient->subject_id,
            'coefficient' => $event->subjectCoefficient->coefficient,
            'school_id' => $event->subjectCoefficient->school_id,
        ]);
    }
}
