<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Contracts;

use App\Domains\Attendance\Domain\Contracts\StudentIdentityContract;
use App\Domains\Attendance\Domain\References\StudentReference;
use App\Domains\Student\Infrastructure\Models\Student;

/**
 * Maps Student-domain models onto the StudentIdentityContract. This is the only
 * place in the Attendance domain allowed to see Student models.
 */
final class StudentIdentityAdapter implements StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference
    {
        $student = Student::query()->forSchool($schoolId)->find($studentId);

        return $student !== null ? $this->toStudent($student) : null;
    }

    public function students(string $schoolId, array $studentIds): array
    {
        if ($studentIds === []) {
            return [];
        }

        return Student::query()
            ->forSchool($schoolId)
            ->whereKey($studentIds)
            ->orderBy('last_name')
            ->get()
            ->map(fn (Student $student): StudentReference => $this->toStudent($student))
            ->all();
    }

    private function toStudent(Student $student): StudentReference
    {
        return new StudentReference(
            id: $student->getKey(),
            fullName: (string) $student->full_name,
            studentNumber: $student->student_number,
            firstName: (string) $student->first_name,
            lastName: (string) $student->last_name,
        );
    }
}
