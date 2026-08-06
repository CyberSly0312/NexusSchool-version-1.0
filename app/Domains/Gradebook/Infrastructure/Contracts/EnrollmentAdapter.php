<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Contracts;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Gradebook\Domain\Contracts\EnrollmentContract;
use App\Domains\Gradebook\Domain\References\EnrollmentReference;
use App\Domains\Gradebook\Domain\References\StudentReference;

/**
 * Maps Enrollment-domain models onto the Gradebook EnrollmentContract. This is
 * the only place in the Gradebook domain allowed to see Enrollment models.
 */
final class EnrollmentAdapter implements EnrollmentContract
{
    public function enrollment(string $schoolId, int|string $enrollmentId): ?EnrollmentReference
    {
        $enrollment = Enrollment::query()
            ->forSchool($schoolId)
            ->with('student')
            ->find($enrollmentId);

        return $enrollment !== null ? $this->toReference($enrollment) : null;
    }

    public function enrollmentsOfClass(
        string $schoolId,
        int|string $classId,
        int|string|null $yearId = null,
        int|string|null $sectionId = null,
    ): array {
        return $this->query($schoolId, $classId, $yearId, $sectionId)
            ->get()
            ->map(fn (Enrollment $enrollment): EnrollmentReference => $this->toReference($enrollment))
            ->all();
    }

    public function studentsOfClass(
        string $schoolId,
        int|string $classId,
        int|string|null $yearId = null,
        int|string|null $sectionId = null,
    ): array {
        return $this->query($schoolId, $classId, $yearId, $sectionId)
            ->get()
            ->map(function (Enrollment $enrollment): StudentReference {
                $student = $enrollment->student;

                return new StudentReference(
                    id: $student?->getKey() ?? $enrollment->student_id,
                    fullName: (string) ($student?->full_name ?? "#{$enrollment->student_id}"),
                    studentNumber: $student?->student_number,
                    firstName: (string) ($student?->first_name ?? ''),
                    lastName: (string) ($student?->last_name ?? ''),
                );
            })
            ->all();
    }

    private function query(string $schoolId, int|string $classId, int|string|null $yearId, int|string|null $sectionId): \Illuminate\Database\Eloquent\Builder
    {
        return Enrollment::query()
            ->forSchool($schoolId)
            ->where('school_class_id', $classId)
            ->where('status', EnrollmentStatus::Active)
            ->when($yearId !== null, fn ($query) => $query->where('academic_calendar_year_id', $yearId))
            ->when($sectionId !== null, fn ($query) => $query->where('class_section_id', $sectionId))
            ->with('student');
    }

    private function toReference(Enrollment $enrollment): EnrollmentReference
    {
        $student = $enrollment->student;

        return new EnrollmentReference(
            id: $enrollment->getKey(),
            studentId: $student?->getKey() ?? $enrollment->student_id,
            studentFullName: (string) ($student?->full_name ?? "#{$enrollment->student_id}"),
            classId: $enrollment->school_class_id,
            sectionId: $enrollment->class_section_id,
            academicYearId: $enrollment->academic_calendar_year_id,
            status: $enrollment->status->value,
        );
    }
}
