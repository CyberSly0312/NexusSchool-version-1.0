<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * A graduation record closing an enrollment — the authoritative graduation
 * verdict, date and education level (SPR-015, supplied by the Enrollment
 * engine).
 */
final readonly class GraduationReference
{
    public function __construct(
        public int|string $id,
        public int|string $enrollmentId,
        public int|string $studentId,
        public string $decision,
        public ?string $graduatedOn = null,
        public ?string $certificateReference = null,
        public int|string|null $educationLevelId = null,
        public ?string $educationLevel = null,
    ) {
    }

    public function isGraduated(): bool
    {
        return $this->decision === 'graduated' || $this->decision === 'conditional';
    }
}
