<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Domain\Events\StudentUpdated;
use App\Domains\Student\Domain\Exceptions\AcademicAssignmentException;
use App\Domains\Student\Domain\Exceptions\DuplicateStudentException;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Domain\ValueObjects\BirthDate;
use App\Domains\Student\Domain\ValueObjects\EmailAddress;
use App\Domains\Student\Domain\ValueObjects\PhoneNumber;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateStudentAction
{
    public function execute(Student $student, UpdateStudentData $data, ?User $actor = null): Student
    {
        if ($student->isImmutable()) {
            throw InvalidStudentStateException::for('update', $student->status->value, 'Archived students are immutable.');
        }

        $this->normalizeValueObjects($data);
        $this->assertNoDuplicate($student, $data);

        if ($data->birthDate !== null) {
            BirthDate::from($data->birthDate);
        }

        if ($data->schoolClassId !== null) {
            $classBelongsToSchool = SchoolClass::query()
                ->forSchool($student->school_id)
                ->whereKey($data->schoolClassId)
                ->exists();

            if (! $classBelongsToSchool) {
                throw AcademicAssignmentException::classMismatch((string) $data->schoolClassId);
            }
        }

        return DB::transaction(function () use ($student, $data, $actor): Student {
            $changes = array_filter(
                $data->toArray(),
                fn ($value) => $value !== null,
            );

            if ($changes !== []) {
                $student->update($changes);
            }

            if ($data->replaceGuardians) {
                $this->replaceGuardians($student, $data->guardians);
            }

            $student->refresh();

            StudentUpdated::dispatch($student, $actor);

            return $student;
        });
    }

    private function normalizeValueObjects(UpdateStudentData $data): void
    {
        if ($data->email !== null && trim($data->email) !== '') {
            new EmailAddress($data->email);
        }

        if ($data->phone !== null && trim($data->phone) !== '') {
            new PhoneNumber($data->phone);
        }
    }

    private function assertNoDuplicate(Student $student, UpdateStudentData $data): void
    {
        if ($data->email !== null && trim($data->email) !== '') {
            $emailTaken = Student::query()
                ->forSchool($student->school_id)
                ->where('email', mb_strtolower(trim($data->email)))
                ->whereKeyNot($student->getKey())
                ->exists();

            if ($emailTaken) {
                throw DuplicateStudentException::email($data->email);
            }
        }
    }

    /**
     * @param  list<\App\Domains\Student\Domain\ValueObjects\GuardianReference>  $guardians
     */
    private function replaceGuardians(Student $student, array $guardians): void
    {
        StudentGuardian::where('student_id', $student->getKey())->delete();

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
