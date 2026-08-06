<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Events\GradebookCreated;
use App\Domains\Gradebook\Domain\Exceptions\GradebookException;
use App\Domains\Gradebook\Domain\ValueObjects\GradeCoefficient;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a draft term/subject gradebook (SPR-010). The academic context is
 * validated through the domain contracts and the scope uniqueness is enforced:
 * one gradebook per year + term + subject + class (+ optional section).
 */
final class CreateGradebookAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
    ) {
    }

    public function execute(CreateGradebookData $data, ?User $actor = null): Gradebook
    {
        return DB::transaction(function () use ($data, $actor): Gradebook {
            $this->assertAcademicContext($data);
            $this->assertUniqueScope($data);

            $coefficient = GradeCoefficient::fromValue($data->coefficient);

            $gradebook = Gradebook::create([
                'school_id' => $data->schoolId,
                'academic_calendar_year_id' => $data->yearId,
                'academic_calendar_term_id' => $data->termId,
                'academic_subject_id' => $data->subjectId,
                'school_class_id' => $data->classId,
                'class_section_id' => $data->sectionId,
                'staff_id' => $data->staffId,
                'coefficient' => $coefficient->value,
                'status' => GradebookStatus::Draft,
                'created_by' => $actor?->getKey(),
                'notes' => $data->notes,
            ]);

            GradebookCreated::dispatch($gradebook, $actor);

            return $gradebook;
        });
    }

    private function assertAcademicContext(CreateGradebookData $data): void
    {
        if ($this->academic->year($data->schoolId, $data->yearId) === null) {
            throw new GradebookException("Academic year [{$data->yearId}] not found.");
        }

        if ($this->academic->term($data->schoolId, $data->termId) === null) {
            throw new GradebookException("Academic term [{$data->termId}] not found.");
        }

        if ($this->academic->subject($data->schoolId, $data->subjectId) === null) {
            throw new GradebookException("Subject [{$data->subjectId}] not found.");
        }

        if ($this->academic->schoolClass($data->schoolId, $data->classId) === null) {
            throw new GradebookException("School class [{$data->classId}] not found.");
        }

        if ($data->sectionId !== null && $this->academic->section($data->schoolId, $data->sectionId) === null) {
            throw new GradebookException("Class section [{$data->sectionId}] not found.");
        }
    }

    private function assertUniqueScope(CreateGradebookData $data): void
    {
        $exists = Gradebook::query()
            ->where('school_id', $data->schoolId)
            ->where('academic_calendar_year_id', $data->yearId)
            ->where('academic_calendar_term_id', $data->termId)
            ->where('academic_subject_id', $data->subjectId)
            ->where('school_class_id', $data->classId)
            ->where('class_section_id', $data->sectionId)
            ->where('status', '!=', GradebookStatus::Archived->value)
            ->exists();

        if ($exists) {
            throw new GradebookException('A gradebook already exists for this period, subject, class and section.');
        }
    }
}
