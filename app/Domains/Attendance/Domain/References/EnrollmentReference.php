<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\References;

/**
 * Read-only projection of an Enrollment (SPR-008). Attendance records are bound
 * to an enrollment, never directly to a student, so a student must be actively
 * enrolled to be marked.
 */
final readonly class EnrollmentReference
{
    public function __construct(
        public int|string $id,
        public int|string $studentId,
        public string $studentFullName,
        public int|string $classId,
        public int|string|null $sectionId,
        public int|string|null $academicYearId,
        public string $status,
    ) {
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
