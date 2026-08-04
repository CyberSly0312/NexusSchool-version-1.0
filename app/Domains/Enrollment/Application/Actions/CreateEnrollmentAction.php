<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentAcademicResolver;
use App\Domains\Enrollment\Application\Services\EnrollmentNumberGenerator;
use App\Domains\Enrollment\Domain\Events\EnrollmentCreated;
use App\Domains\Enrollment\Domain\Exceptions\DuplicateEnrollmentException;
use App\Domains\Enrollment\Domain\Exceptions\InvalidEnrollmentDateException;
use App\Domains\Enrollment\Domain\Rules\UniqueActiveEnrollmentRule;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateEnrollmentAction
{
    public function __construct(
        private readonly EnrollmentNumberGenerator $numberGenerator,
        private readonly EnrollmentAcademicResolver $resolver,
    ) {
    }

    public function execute(CreateEnrollmentData $data, ?User $actor = null): Enrollment
    {
        $data = $this->ensureEnrollmentNumber($data);

        $this->assertEnrollmentNumberUnique($data);
        $this->resolver->assertActiveAcademicYear($data->academicCalendarYearId);
        $this->assertStudentInSchool($data);
        $this->assertAcademicStructure($data);
        $this->assertNoActiveDuplicate($data);
        $this->assertEnrollmentDateInYear($data);

        return DB::transaction(function () use ($data, $actor): Enrollment {
            $enrollment = Enrollment::create($data->toArray());

            EnrollmentCreated::dispatch($enrollment, $actor);

            return $enrollment;
        });
    }

    private function ensureEnrollmentNumber(CreateEnrollmentData $data): CreateEnrollmentData
    {
        if ($data->enrollmentNumber !== null && trim($data->enrollmentNumber) !== '') {
            return $data;
        }

        return new CreateEnrollmentData(
            schoolId: $data->schoolId,
            studentId: $data->studentId,
            academicCalendarYearId: $data->academicCalendarYearId,
            academicProgramId: $data->academicProgramId,
            educationLevelId: $data->educationLevelId,
            schoolClassId: $data->schoolClassId,
            classSectionId: $data->classSectionId,
            enrollmentType: $data->enrollmentType,
            enrolledAt: $data->enrolledAt,
            enrollmentNumber: $this->numberGenerator->generate($data->schoolId),
            notes: $data->notes,
        );
    }

    private function assertEnrollmentNumberUnique(CreateEnrollmentData $data): void
    {
        $taken = Enrollment::query()
            ->forSchool($data->schoolId)
            ->where('enrollment_number', $data->enrollmentNumber)
            ->exists();

        if ($taken) {
            throw DuplicateEnrollmentException::enrollmentNumber((string) $data->enrollmentNumber);
        }
    }

    private function assertAcademicStructure(CreateEnrollmentData $data): void
    {
        $class = $this->resolver->assertClassInSchool($data->schoolId, $data->schoolClassId);
        $this->resolver->assertProgramInSchool($data->schoolId, $data->academicProgramId);
        $this->resolver->assertEducationLevelInSchool($data->schoolId, $data->educationLevelId);
        $this->resolver->assertSectionBelongsToClass($class, $data->classSectionId);
    }

    private function assertStudentInSchool(CreateEnrollmentData $data): void
    {
        $student = Student::query()
            ->forSchool($data->schoolId)
            ->find($data->studentId);

        if ($student === null) {
            throw new InvalidArgumentException(
                "The student #{$data->studentId} does not belong to school {$data->schoolId}.",
            );
        }
    }

    private function assertNoActiveDuplicate(CreateEnrollmentData $data): void
    {
        if ((new UniqueActiveEnrollmentRule($data->studentId, $data->academicCalendarYearId))->fails()) {
            throw DuplicateEnrollmentException::activeEnrollment($data->studentId, $data->academicCalendarYearId);
        }
    }

    private function assertEnrollmentDateInYear(CreateEnrollmentData $data): void
    {
        if ($data->enrolledAt === null || trim($data->enrolledAt) === '') {
            return;
        }

        $year = AcademicCalendarYear::query()->find($data->academicCalendarYearId);
        $enrolled = CarbonImmutable::parse($data->enrolledAt);
        $yearStart = $year->start_date ? CarbonImmutable::parse($year->start_date) : null;
        $yearEnd = $year->end_date ? CarbonImmutable::parse($year->end_date) : null;

        if (($yearStart !== null && $enrolled->lt($yearStart)) || ($yearEnd !== null && $enrolled->gt($yearEnd))) {
            throw InvalidEnrollmentDateException::message(
                "The enrollment date ({$data->enrolledAt}) must fall within the academic year {$year->name}.",
            );
        }
    }
}
