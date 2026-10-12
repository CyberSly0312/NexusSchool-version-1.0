<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * Composition-grade enrollment facts of a student's retained history (SPR-015,
 * supplied by the Enrollment engine through the EnrollmentContract).
 */
final readonly class EnrollmentReference
{
    public function __construct(
        public int|string $id,
        public int|string $studentId,
        public int|string|null $yearId = null,
        public int|string|null $classId = null,
        public ?string $className = null,
        public int|string|null $sectionId = null,
        public ?string $sectionName = null,
        public int|string|null $educationLevelId = null,
        public ?string $educationLevel = null,
        public ?string $status = null,
        public ?string $enrollmentNumber = null,
    ) {
    }
}
