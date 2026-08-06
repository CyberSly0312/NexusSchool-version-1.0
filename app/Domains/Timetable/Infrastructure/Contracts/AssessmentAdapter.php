<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Timetable\Domain\Contracts\AssessmentContract;
use App\Domains\Timetable\Domain\References\AssessmentReference;

/**
 * Maps the Assessment engine model (SPR-009) onto the AssessmentContract. This
 * adapter is the only place in the Timetable domain allowed to see assessment
 * models.
 */
final class AssessmentAdapter implements AssessmentContract
{
    public function assessments(string $schoolId): array
    {
        return Assessment::query()
            ->where('school_id', $schoolId)
            ->orderBy('title')
            ->get()
            ->map(fn (Assessment $assessment): AssessmentReference => $this->toAssessment($assessment))
            ->all();
    }

    public function assessment(string $schoolId, string $assessmentId): ?AssessmentReference
    {
        $assessment = Assessment::query()
            ->where('school_id', $schoolId)
            ->find($assessmentId);

        return $assessment !== null ? $this->toAssessment($assessment) : null;
    }

    private function toAssessment(Assessment $assessment): AssessmentReference
    {
        return new AssessmentReference(
            id: (string) $assessment->getKey(),
            name: (string) $assessment->title,
            code: (string) $assessment->uuid,
            subjectId: $assessment->academic_subject_id,
            isActive: $assessment->status === AssessmentStatus::Published,
        );
    }
}
