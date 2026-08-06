<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Gradebook\Domain\Contracts\AssessmentContract;
use App\Domains\Gradebook\Domain\References\AssessmentReference;

/**
 * Maps the Assessment engine models onto the Gradebook AssessmentContract
 * (SPR-010). This is the only place in the Gradebook domain allowed to see
 * Assessment models. Only published or completed assessments feed the term
 * average, and only scores that pass validation (a below-passing score is a
 * legitimate fail, not an error).
 */
final class AssessmentAdapter implements AssessmentContract
{
    private const INCLUDED_ASSESSMENT_STATUSES = [
        AssessmentStatus::Published->value,
        AssessmentStatus::Completed->value,
    ];

    public function studentAssessmentContributions(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
        int|string $subjectId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): array {
        $enrollmentIds = $this->studentEnrollmentIds($schoolId, $studentId, $classId, $yearId, $sectionId);

        if ($enrollmentIds === []) {
            return [];
        }

        $assessments = $this->assessments($schoolId, $yearId, $termId, $subjectId, $classId, $sectionId);

        if ($assessments->isEmpty()) {
            return [];
        }

        $scores = AssessmentScore::query()
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->whereIn('enrollment_id', $enrollmentIds)
            ->whereNotIn('validation_status', [
                ScoreValidationStatus::AboveMaximum->value,
                ScoreValidationStatus::BelowZero->value,
            ])
            ->get()
            ->groupBy('assessment_id');

        $contributions = [];

        foreach ($assessments as $assessment) {
            $group = $scores->get($assessment->getKey(), collect());

            if ($group->isEmpty()) {
                continue;
            }

            $percentages = $group->pluck('percentage')
                ->map(fn ($percentage): float => (float) $percentage)
                ->filter(fn (float $percentage): bool => is_finite($percentage))
                ->all();

            if ($percentages === []) {
                continue;
            }

            $contributions[] = new AssessmentReference(
                id: $assessment->getKey(),
                title: (string) $assessment->title,
                percentage: (float) round(array_sum($percentages) / count($percentages), (int) config('nexus-gradebook.grade_system.decimal_places', 2)),
                weight: (float) ($assessment->weight ?? 1.0),
                maximumScore: (float) $assessment->maximum_score,
                assessmentTypeId: $assessment->assessment_type_id,
            );
        }

        return $contributions;
    }

    public function hasPublishedAssessments(
        string $schoolId,
        int|string $yearId,
        int|string $termId,
        int|string $subjectId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): bool {
        return $this->assessments($schoolId, $yearId, $termId, $subjectId, $classId, $sectionId)->isNotEmpty();
    }

    /**
     * @return list<int|string>
     */
    private function studentEnrollmentIds(
        string $schoolId,
        int|string $studentId,
        int|string $classId,
        int|string $yearId,
        int|string|null $sectionId,
    ): array {
        return Enrollment::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->where('school_class_id', $classId)
            ->where('academic_calendar_year_id', $yearId)
            ->when($sectionId !== null, fn ($query) => $query->where('class_section_id', $sectionId))
            ->pluck('id')
            ->all();
    }

    private function assessments(
        string $schoolId,
        int|string $yearId,
        int|string $termId,
        int|string $subjectId,
        int|string $classId,
        int|string|null $sectionId,
    ): \Illuminate\Database\Eloquent\Collection {
        return Assessment::query()
            ->forSchool($schoolId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('academic_calendar_term_id', $termId)
            ->where('academic_subject_id', $subjectId)
            ->where('school_class_id', $classId)
            ->where('class_section_id', $sectionId)
            ->whereIn('status', self::INCLUDED_ASSESSMENT_STATUSES)
            ->get();
    }
}
