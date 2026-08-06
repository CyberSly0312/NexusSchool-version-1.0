<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Services;

use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Events\AssessmentCompleted;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Models\User;

/**
 * Drives the "all expected students scored" completion rule (SPR-009). A
 * published assessment completes as soon as every active enrollment of its
 * class holds at least one score row.
 */
class AssessmentCompletionService
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
    ) {
    }

    public function completeIfExpected(Assessment $assessment, ?User $actor = null): void
    {
        if ($assessment->status !== AssessmentStatus::Published) {
            return;
        }

        $enrollments = $this->enrollment->enrollmentsOfClass(
            $assessment->school_id,
            $assessment->school_class_id,
            $assessment->academic_calendar_year_id,
        );

        if ($enrollments === []) {
            return;
        }

        $scored = AssessmentScore::query()
            ->where('assessment_id', $assessment->getKey())
            ->distinct()
            ->count('enrollment_id');

        if ($scored >= count($enrollments)) {
            $assessment->update([
                'status' => AssessmentStatus::Completed,
                'completed_at' => now(),
            ]);

            AssessmentCompleted::dispatch($assessment->refresh(), $scored, $actor);
        }
    }
}
