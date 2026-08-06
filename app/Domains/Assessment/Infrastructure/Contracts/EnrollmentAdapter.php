<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Contracts\EnrollmentContract;
use App\Domains\Assessment\Domain\References\EnrollmentReference;
use App\Domains\Assessment\Domain\References\StudentReference;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

/**
 * Maps Enrollment-domain models onto the EnrollmentContract. This is the only
 * place in the Assessment domain allowed to see Enrollment models.
 *
 * Scores are bound to enrollments, so the adapter resolves "who is in which
 * class" through active enrollments only.
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

    public function enrollmentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array
    {
        return $this->query($schoolId, $classId, $yearId)
            ->get()
            ->map(fn (Enrollment $enrollment): EnrollmentReference => $this->toReference($enrollment))
            ->all();
    }

    public function studentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array
    {
        return $this->query($schoolId, $classId, $yearId)
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

    public function isStudentEnrolled(string $schoolId, int|string $studentId, int|string $classId): bool
    {
        return Enrollment::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->where('school_class_id', $classId)
            ->where('status', EnrollmentStatus::Active)
            ->exists();
    }

    private function query(string $schoolId, int|string $classId, int|string|null $yearId): \Illuminate\Database\Eloquent\Builder
    {
        return Enrollment::query()
            ->forSchool($schoolId)
            ->where('school_class_id', $classId)
            ->where('status', EnrollmentStatus::Active)
            ->when($yearId !== null, fn ($query) => $query->where('academic_calendar_year_id', $yearId))
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
