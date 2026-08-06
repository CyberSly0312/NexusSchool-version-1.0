<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of an enrollment owned by the Enrollment domain
 * (SPR-010). Gradebook entries are bound to the enrollment that placed the
 * student in the class.
 */
final readonly class EnrollmentReference
{
    public function __construct(
        public int|string $id,
        public int|string $studentId,
        public string $studentFullName,
        public int|string $classId,
        public int|string|null $sectionId = null,
        public int|string|null $academicYearId = null,
        public string $status = 'active',
    ) {
    }
}
