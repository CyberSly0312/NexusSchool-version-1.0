<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\References\StudentReference;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Student\Infrastructure\Models\Student;

/**
 * Maps Student/Enrollment-domain models onto the StudentIdentityContract
 * (SPR-012). This is the only place in the Communication domain allowed to see
 * Student and Enrollment models.
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

    public function studentsInClass(string $schoolId, int|string $classId, int|string|null $sectionId = null): array
    {
        $query = Enrollment::query()
            ->forSchool($schoolId)
            ->where('school_class_id', $classId)
            ->where('status', 'active');

        if ($sectionId !== null) {
            $query->where('class_section_id', $sectionId);
        }

        $studentIds = $query->pluck('student_id')->all();

        return $this->students($schoolId, $studentIds);
    }

    private function toStudent(Student $student): StudentReference
    {
        return new StudentReference(
            id: $student->getKey(),
            fullName: (string) $student->full_name,
            studentNumber: $student->student_number,
            firstName: (string) $student->first_name,
            lastName: (string) $student->last_name,
            class: $student->schoolClass?->name,
            section: $student->section?->name,
            dateOfBirth: $student->date_of_birth?->toDateString(),
            gender: $student->gender?->value,
            userId: $student->user_id,
        );
    }
}
