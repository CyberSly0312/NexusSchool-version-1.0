<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Student\Application\DTOs\CreateStudentData;
use App\Domains\Student\Application\Services\StudentNumberGenerator;
use App\Domains\Student\Domain\Events\StudentCreated;
use App\Domains\Student\Domain\Exceptions\AcademicAssignmentException;
use App\Domains\Student\Domain\Exceptions\DuplicateStudentException;
use App\Domains\Student\Domain\ValueObjects\BirthDate;
use App\Domains\Student\Domain\ValueObjects\EmailAddress;
use App\Domains\Student\Domain\ValueObjects\PhoneNumber;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateStudentAction
{
    public function __construct(
        private readonly StudentNumberGenerator $numberGenerator,
    ) {
    }

    public function execute(CreateStudentData $data, ?User $actor = null): Student
    {
        $data = $this->ensureStudentNumber($data);

        $this->normalizeValueObjects($data);
        $this->assertNoDuplicate($data);
        $this->assertValidBirthDate($data->birthDate);
        $this->assertValidAcademicAssignment($data->schoolId, $data);

        return DB::transaction(function () use ($data, $actor): Student {
            $attributes = $data->toArray();
            unset($attributes['guardians']);

            $student = Student::create($attributes);

            $this->syncGuardians($student, $data->guardians);

            StudentCreated::dispatch($student, $actor);

            return $student;
        });
    }

    private function ensureStudentNumber(CreateStudentData $data): CreateStudentData
    {
        if ($data->studentNumber !== null && trim($data->studentNumber) !== '') {
            return $data;
        }

        return new CreateStudentData(
            schoolId: $data->schoolId,
            firstName: $data->firstName,
            lastName: $data->lastName,
            studentNumber: $this->numberGenerator->generate($data->schoolId),
            middleName: $data->middleName,
            preferredName: $data->preferredName,
            gender: $data->gender,
            birthDate: $data->birthDate,
            nationality: $data->nationality,
            email: $data->email,
            phone: $data->phone,
            admissionDate: $data->admissionDate,
            admissionNumber: $data->admissionNumber,
            academicCalendarYearId: $data->academicCalendarYearId,
            schoolClassId: $data->schoolClassId,
            classSectionId: $data->classSectionId,
            nationalId: $data->nationalId,
            birthCertificateReference: $data->birthCertificateReference,
            photoPath: $data->photoPath,
            addressStreet: $data->addressStreet,
            addressCity: $data->addressCity,
            addressState: $data->addressState,
            addressPostalCode: $data->addressPostalCode,
            addressCountry: $data->addressCountry,
            emergencyContact: $data->emergencyContact,
            guardians: $data->guardians,
            medicalNotes: $data->medicalNotes,
            studentStatus: $data->studentStatus,
            transferStatus: $data->transferStatus,
            transferFromSchool: $data->transferFromSchool,
        );
    }

    private function normalizeValueObjects(CreateStudentData $data): void
    {
        if ($data->email !== null && trim($data->email) !== '') {
            new EmailAddress($data->email);
        }

        if ($data->phone !== null && trim($data->phone) !== '') {
            new PhoneNumber($data->phone);
        }
    }

    private function assertNoDuplicate(CreateStudentData $data): void
    {
        if ($data->studentNumber !== null && trim($data->studentNumber) !== '') {
            $numberTaken = Student::query()
                ->forSchool($data->schoolId)
                ->where('student_number', $data->studentNumber)
                ->exists();

            if ($numberTaken) {
                throw DuplicateStudentException::studentNumber($data->studentNumber);
            }
        }

        if ($data->email !== null && trim($data->email) !== '') {
            $emailTaken = Student::query()
                ->forSchool($data->schoolId)
                ->where('email', mb_strtolower(trim($data->email)))
                ->exists();

            if ($emailTaken) {
                throw DuplicateStudentException::email($data->email);
            }
        }
    }

    private function assertValidBirthDate(?string $birthDate): void
    {
        if ($birthDate === null) {
            return;
        }

        BirthDate::from($birthDate);
    }

    private function assertValidAcademicAssignment(string $schoolId, CreateStudentData $data): void
    {
        if ($data->academicCalendarYearId === null || $data->schoolClassId === null) {
            return;
        }

        $year = AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->find($data->academicCalendarYearId);

        if ($year === null) {
            throw AcademicAssignmentException::missingYear($schoolId);
        }

        if (! $year->isActive()) {
            throw AcademicAssignmentException::inactiveYear($year->name);
        }

        if ($data->admissionDate !== null) {
            $admission = CarbonImmutable::parse($data->admissionDate);
            $yearStart = $year->start_date ? CarbonImmutable::parse($year->start_date) : null;
            $yearEnd = $year->end_date ? CarbonImmutable::parse($year->end_date) : null;

            if (($yearStart !== null && $admission->lt($yearStart)) || ($yearEnd !== null && $admission->gt($yearEnd))) {
                throw new InvalidArgumentException(
                    "The admission date ({$data->admissionDate}) must fall within the academic year {$year->name}.",
                );
            }
        }

        $schoolClass = SchoolClass::query()
            ->forSchool($schoolId)
            ->find($data->schoolClassId);

        if ($schoolClass === null) {
            throw AcademicAssignmentException::classMismatch((string) $data->schoolClassId);
        }

        if ($data->classSectionId !== null) {
            $sectionBelongsToClass = $schoolClass->sections()
                ->whereKey($data->classSectionId)
                ->exists();

            if (! $sectionBelongsToClass) {
                throw new InvalidArgumentException('The class section does not belong to the assigned class.');
            }
        }
    }

    /**
     * @param  list<\App\Domains\Student\Domain\ValueObjects\GuardianReference>  $guardians
     */
    private function syncGuardians(Student $student, array $guardians): void
    {
        foreach ($guardians as $index => $guardian) {
            StudentGuardian::create([
                'school_id' => $student->school_id,
                'student_id' => $student->getKey(),
                ...$guardian->toArray(),
                'is_primary' => $this->isPrimaryGuardian($guardians, $index),
            ]);
        }
    }

    /**
     * The first guardian is primary unless another one is explicitly flagged.
     *
     * @param  list<\App\Domains\Student\Domain\ValueObjects\GuardianReference>  $guardians
     */
    private function isPrimaryGuardian(array $guardians, int $index): bool
    {
        $hasExplicitPrimary = false;

        foreach ($guardians as $guardian) {
            if ($guardian->isPrimary) {
                $hasExplicitPrimary = true;

                break;
            }
        }

        return $hasExplicitPrimary ? $guardians[$index]->isPrimary : $index === 0;
    }
}
