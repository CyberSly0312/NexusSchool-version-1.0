<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * Read-only projection of an Enrollment (SPR-009). Scores are bound to an
 * enrollment, never directly to a student, so a student must be actively
 * enrolled in the assessment's class to be graded.
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
