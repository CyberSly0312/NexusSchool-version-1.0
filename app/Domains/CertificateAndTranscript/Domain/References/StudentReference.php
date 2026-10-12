<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * Identity of the student a completion document is issued for (SPR-015).
 */
final readonly class StudentReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public string $studentNumber,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $class = null,
        public ?string $section = null,
        public ?string $dateOfBirth = null,
        public ?string $gender = null,
        public int|string|null $userId = null,
    ) {
    }
}
