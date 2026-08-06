<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\RecordScoreData;
use App\Domains\Assessment\Application\Services\AssessmentCompletionService;
use App\Domains\Assessment\Application\Services\ScoreValidationService;
use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\Contracts\StaffContract;
use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Events\ScoreRecorded;
use App\Domains\Assessment\Domain\Events\ScoreUpdated;
use App\Domains\Assessment\Domain\Exceptions\ScoreOutOfRangeException;
use App\Domains\Assessment\Domain\Exceptions\StudentNotEnrolledException;
use App\Domains\Assessment\Domain\Exceptions\TeacherNotAuthorizedException;
use App\Domains\Assessment\Domain\ValueObjects\ScorePercentage;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Records or updates a single student's score on an assessment (SPR-009).
 *
 * The student is resolved through their enrollment, never directly; a score
 * is rejected when it falls outside [0, maximum]. Teachers may only grade the
 * subjects they are assigned to. When the last expected score arrives the
 * assessment auto-completes.
 */
final class RecordScoreAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
        private readonly StaffContract $staff,
        private readonly ScoreValidationService $validation,
        private readonly AssessmentCompletionService $completion,
    ) {
    }

    public function execute(RecordScoreData $data, ?User $actor = null): AssessmentScore
    {
        return DB::transaction(function () use ($data, $actor): AssessmentScore {
            $assessment = Assessment::query()->findOrFail($data->assessmentId);

            $assessment->assertScoreable();

            $maximum = $this->resolvedMaximum($assessment, $data->componentId);

            $this->assertEnrollmentInClass($assessment, $data->enrollmentId);
            $this->assertScoreRange($data->score, $maximum);
            $this->assertTeacherAssignment($assessment, $actor);

            $existing = $this->existingScore($assessment, $data);

            $score = $existing !== null
                ? $this->updateScore($assessment, $existing, $data, $maximum, $actor)
                : $this->createScore($assessment, $data, $maximum, $actor);

            $this->completion->completeIfExpected($assessment, $actor);

            return $score;
        });
    }

    private function assertEnrollmentInClass(Assessment $assessment, int|string $enrollmentId): void
    {
        $enrollment = $this->enrollment->enrollment($assessment->school_id, $enrollmentId);

        if ($enrollment === null || ! $enrollment->isActive() || $enrollment->classId !== $assessment->school_class_id) {
            throw StudentNotEnrolledException::forEnrollment($enrollmentId, $assessment->school_class_id);
        }
    }

    private function assertScoreRange(float $score, float $maximum): void
    {
        $status = $this->validation->validate($score, $maximum);

        if ($status->isInvalid()) {
            throw ScoreOutOfRangeException::forValue($score, $maximum);
        }
    }

    private function assertTeacherAssignment(Assessment $assessment, ?User $actor): void
    {
        if ($actor === null || $actor->hasRole('super_admin')) {
            return;
        }

        $teacher = $this->staff->teacherForUser($assessment->school_id, $actor->getKey());

        if ($teacher === null) {
            return;
        }

        if (! in_array($assessment->academic_subject_id, $teacher->subjectIds, false)) {
            throw TeacherNotAuthorizedException::forSubject($assessment->academic_subject_id);
        }
    }

    private function resolvedMaximum(Assessment $assessment, int|string|null $componentId): float
    {
        if ($componentId === null) {
            return (float) $assessment->maximum_score;
        }

        $component = $assessment->components()->find($componentId);

        return $component !== null ? (float) $component->maximum_score : (float) $assessment->maximum_score;
    }

    private function existingScore(Assessment $assessment, RecordScoreData $data): ?AssessmentScore
    {
        return AssessmentScore::query()
            ->where('assessment_id', $assessment->getKey())
            ->where('assessment_component_id', $data->componentId)
            ->where('enrollment_id', $data->enrollmentId)
            ->first();
    }

    private function createScore(
        Assessment $assessment,
        RecordScoreData $data,
        float $maximum,
        ?User $actor,
    ): AssessmentScore {
        $score = AssessmentScore::create([
            'school_id' => $assessment->school_id,
            'assessment_id' => $assessment->getKey(),
            'assessment_component_id' => $data->componentId,
            'enrollment_id' => $data->enrollmentId,
            'score' => $data->score,
            'percentage' => ScorePercentage::fromScore($data->score, $maximum)->value,
            'status' => ScoreStatus::Draft,
            'validation_status' => $this->validation->validate($data->score, $maximum),
            'feedback' => $data->feedback,
            'graded_by' => $actor?->getKey(),
            'graded_at' => now(),
        ]);

        ScoreRecorded::dispatch($assessment, $score, $data->enrollmentId, $actor);

        return $score;
    }

    private function updateScore(
        Assessment $assessment,
        AssessmentScore $score,
        RecordScoreData $data,
        float $maximum,
        ?User $actor,
    ): AssessmentScore {
        $score->assertMutable();

        $previous = (float) $score->score;

        $score->update([
            'score' => $data->score,
            'percentage' => ScorePercentage::fromScore($data->score, $maximum)->value,
            'validation_status' => $this->validation->validate($data->score, $maximum),
            'feedback' => $data->feedback,
            'graded_by' => $actor?->getKey(),
            'graded_at' => now(),
        ]);

        ScoreUpdated::dispatch($assessment, $score->refresh(), $data->enrollmentId, $previous, $actor);

        return $score;
    }
}
