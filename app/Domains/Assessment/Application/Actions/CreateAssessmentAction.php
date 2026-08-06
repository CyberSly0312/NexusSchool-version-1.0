<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\CreateAssessmentData;
use App\Domains\Assessment\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Assessment\Domain\Contracts\StaffContract;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Events\AssessmentCreated;
use App\Domains\Assessment\Domain\Exceptions\WeightTotalExceededException;
use App\Domains\Assessment\Domain\ValueObjects\MaximumScore;
use App\Domains\Assessment\Domain\ValueObjects\PassingScore;
use App\Domains\Assessment\Domain\ValueObjects\Weight;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a draft assessment bound to one subject, one academic period and one
 * teacher (SPR-009). The academic context, teacher and weight policy are
 * validated through the domain contracts before the aggregate is written.
 */
final class CreateAssessmentAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
        private readonly StaffContract $staff,
    ) {
    }

    public function execute(CreateAssessmentData $data, ?User $actor = null): Assessment
    {
        return DB::transaction(function () use ($data, $actor): Assessment {
            $this->assertAcademicContext($data);
            $this->assertTeacher($data);
            $this->assertWeightPolicy($data, null);

            $maximum = MaximumScore::fromFloat($data->maximumScore);
            $passing = $data->passingScore !== null
                ? PassingScore::fromFloat($data->passingScore, $maximum->value)
                : null;
            $weight = $data->weight !== null ? Weight::fromPercent($data->weight) : null;

            $assessment = Assessment::create([
                'school_id' => $data->schoolId,
                'academic_calendar_year_id' => $data->yearId,
                'academic_calendar_term_id' => $data->termId,
                'academic_subject_id' => $data->subjectId,
                'school_class_id' => $data->classId,
                'class_section_id' => $data->sectionId,
                'staff_id' => $data->staffId,
                'assessment_type_id' => $data->assessmentTypeId,
                'title' => $data->title,
                'description' => $data->description,
                'maximum_score' => $maximum->value,
                'passing_score' => $passing?->value,
                'weight' => $weight?->value,
                'status' => AssessmentStatus::Draft,
                'scheduled_at' => $data->scheduledAt,
                'created_by' => $actor?->getKey(),
                'notes' => $data->notes,
            ]);

            AssessmentCreated::dispatch($assessment, $actor);

            return $assessment;
        });
    }

    private function assertAcademicContext(CreateAssessmentData $data): void
    {
        if ($this->academic->year($data->schoolId, $data->yearId) === null) {
            throw new \App\Domains\Assessment\Domain\Exceptions\AssessmentException("Academic year [{$data->yearId}] not found.");
        }

        if ($this->academic->term($data->schoolId, $data->termId) === null) {
            throw new \App\Domains\Assessment\Domain\Exceptions\AssessmentException("Academic term [{$data->termId}] not found.");
        }

        if ($this->academic->subject($data->schoolId, $data->subjectId) === null) {
            throw new \App\Domains\Assessment\Domain\Exceptions\AssessmentException("Subject [{$data->subjectId}] not found.");
        }
    }

    private function assertTeacher(CreateAssessmentData $data): void
    {
        if ($data->staffId === null) {
            return;
        }

        if (! $this->staff->isTeacher($data->schoolId, $data->staffId)) {
            throw new \App\Domains\Assessment\Domain\Exceptions\AssessmentException("Staff [{$data->staffId}] is not an active teacher.");
        }
    }

    private function assertWeightPolicy(CreateAssessmentData $data, ?float $replacedWeight): void
    {
        $weight = $data->weight;

        if ($weight === null) {
            return;
        }

        $enforced = (bool) config('nexus-assessment.weights.enforce_total', true);

        if (! $enforced) {
            return;
        }

        $ceiling = (float) config('nexus-assessment.weights.max_total', 100.0);
        $current = Assessment::query()
            ->where('school_id', $data->schoolId)
            ->where('academic_subject_id', $data->subjectId)
            ->where('academic_calendar_term_id', $data->termId)
            ->where('status', '!=', AssessmentStatus::Archived->value)
            ->whereNotNull('weight')
            ->sum('weight');

        $total = $current - (float) $replacedWeight + $weight;

        if ($total > $ceiling) {
            throw WeightTotalExceededException::forTotal($total, $ceiling);
        }
    }
}
