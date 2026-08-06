<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Contracts;

use App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract;
use App\Domains\ClassCouncil\Domain\References\EnrollmentReference;
use App\Domains\ClassCouncil\Domain\References\StudentReference;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

/**
 * EnrollmentContract implementation (SPR-013). Provides the active students of
 * a council's class through the Enrollment models.
 */
final class EnrollmentAdapter implements EnrollmentContract
{
    public function activeEnrollments(
        string $schoolId,
        int|string $yearId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): array {
        $query = Enrollment::query()
            ->forSchool($schoolId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('school_class_id', $classId)
            ->where('status', EnrollmentStatus::Active->value);

        if ($sectionId !== null) {
            $query->where('class_section_id', $sectionId);
        }

        return $query->with('student')
            ->orderBy('id')
            ->get()
            ->map(function (Enrollment $enrollment): EnrollmentReference {
                $student = $enrollment->student;

                return new EnrollmentReference(
                    id: $enrollment->getKey(),
                    student: new StudentReference(
                        id: $student?->getKey() ?? $enrollment->student_id,
                        fullName: (string) ($student?->full_name ?? 'Élève'),
                        studentNumber: (string) ($student?->student_number ?? ''),
                        userId: $student?->user_id,
                    ),
                );
            })
            ->all();
    }
}
