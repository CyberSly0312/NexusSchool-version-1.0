<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\Services\EnrollmentAcademicResolver;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\Events\StudentRepeated;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Repeats the current class for a student: the source enrollment is archived
 * and a NEW enrollment row is created for the same class in the target year.
 */
final class RepeatStudentAction
{
    public function __construct(
        private readonly CreateEnrollmentAction $createEnrollment,
        private readonly EnrollmentAcademicResolver $resolver,
    ) {
    }

    public function execute(Enrollment $enrollment, PromotionData $data, ?User $actor = null): Enrollment
    {
        if ($enrollment->isImmutable() || ! $enrollment->isActive()) {
            throw EnrollmentStateException::transition($enrollment->status, 'repeated');
        }

        if ($data->decision !== PromotionDecision::Repeated) {
            throw new InvalidArgumentException('The repeat flow requires the repeated decision.');
        }

        $this->resolver->assertActiveAcademicYear($data->academicCalendarYearId);

        $fromClass = $this->resolver->assertClassInSchool($enrollment->school_id, $enrollment->school_class_id);

        $toClassId = $data->toClassId ?? $enrollment->school_class_id;

        $toClass = $this->resolver->assertClassInSchool($enrollment->school_id, $toClassId);

        $this->resolver->assertSectionBelongsToClass($toClass, $data->toSectionId);

        return DB::transaction(function () use ($enrollment, $data, $fromClass, $toClass, $actor): Enrollment {
            $previous = $this->archive($enrollment, $data->promotedOn);

            $promotion = EnrollmentPromotion::create([
                'school_id' => $previous->school_id,
                'enrollment_id' => $previous->getKey(),
                'student_id' => $previous->student_id,
                'academic_calendar_year_id' => $data->academicCalendarYearId,
                'from_class_id' => $fromClass->getKey(),
                'to_class_id' => $toClass->getKey(),
                'decision' => $data->decision->value,
                'promoted_on' => $data->promotedOn,
                'academic_result' => $data->result?->toArray(),
                'notes' => $data->notes,
            ]);

            $this->createEnrollment->execute(
                new CreateEnrollmentData(
                    schoolId: $previous->school_id,
                    studentId: $previous->student_id,
                    academicCalendarYearId: $data->academicCalendarYearId,
                    academicProgramId: $toClass->academic_program_id,
                    educationLevelId: $toClass->education_level_id,
                    schoolClassId: $toClass->getKey(),
                    classSectionId: $data->toSectionId,
                    enrollmentType: EnrollmentType::Repetition,
                    enrolledAt: $data->promotedOn,
                    notes: $data->notes,
                ),
                $actor,
            );

            StudentRepeated::dispatch($previous, $promotion, $actor);

            return $previous;
        });
    }

    private function archive(Enrollment $enrollment, string $promotedOn): Enrollment
    {
        $enrollment->status = EnrollmentStatus::Archived;
        $enrollment->exited_at = $promotedOn;
        $enrollment->save();

        return $enrollment;
    }
}
