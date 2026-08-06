<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\Timetable\Domain\Contracts\StudentIdentityContract;
use App\Domains\Timetable\Domain\References\StudentReference;
use App\Models\Student;

/**
 * Maps the legacy Student model onto the StudentIdentityContract. This is the
 * only place in the Timetable domain allowed to see Student models.
 */
final class StudentIdentityAdapter implements StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference
    {
        $student = Student::query()
            ->where('school_id', $schoolId)
            ->find($studentId);

        return $student !== null ? $this->toStudent($student) : null;
    }

    public function students(string $schoolId): array
    {
        return Student::query()
            ->where('school_id', $schoolId)
            ->orderBy('last_name')
            ->get()
            ->map(fn (Student $student): StudentReference => $this->toStudent($student))
            ->all();
    }

    private function toStudent(Student $student): StudentReference
    {
        return new StudentReference(
            id: $student->getKey(),
            fullName: trim(implode(' ', array_filter([$student->first_name, $student->last_name]))),
            studentNumber: $student->student_number,
            firstName: (string) $student->first_name,
            lastName: (string) $student->last_name,
        );
    }
}
