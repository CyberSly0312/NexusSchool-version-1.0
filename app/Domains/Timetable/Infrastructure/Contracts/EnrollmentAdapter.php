<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\Timetable\Domain\Contracts\EnrollmentContract;
use App\Domains\Timetable\Domain\References\StudentReference;
use App\Models\StudentEnrollment;

/**
 * Maps the legacy enrollment model onto the EnrollmentContract. This is the only
 * place in the Timetable domain allowed to see enrollment models.
 */
final class EnrollmentAdapter implements EnrollmentContract
{
    public function studentsOfClass(string $schoolId, int|string $classId): array
    {
        return StudentEnrollment::query()
            ->where('school_id', $schoolId)
            ->where('academic_class_id', $classId)
            ->where('status', EnrollmentStatus::Active)
            ->with('student')
            ->orderBy('enrollment_date')
            ->get()
            ->map(function (StudentEnrollment $enrollment): StudentReference {
                $student = $enrollment->student;

                return new StudentReference(
                    id: $student->getKey(),
                    fullName: trim(implode(' ', array_filter([$student->first_name, $student->last_name]))),
                    studentNumber: $student->student_number,
                    firstName: (string) $student->first_name,
                    lastName: (string) $student->last_name,
                );
            })
            ->all();
    }

    public function isStudentEnrolled(string $schoolId, int|string $studentId, int|string $classId): bool
    {
        return StudentEnrollment::query()
            ->where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('academic_class_id', $classId)
            ->where('status', EnrollmentStatus::Active)
            ->exists();
    }
}
