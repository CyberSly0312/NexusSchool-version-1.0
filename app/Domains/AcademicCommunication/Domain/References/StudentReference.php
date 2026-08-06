<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of a student owned by the Student identity domain
 * (SPR-012). Free of any concrete model.
 */
final readonly class StudentReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public ?string $studentNumber = null,
        public string $firstName = '',
        public string $lastName = '',
        public ?string $class = null,
        public ?string $section = null,
        public ?string $dateOfBirth = null,
        public ?string $gender = null,
        public int|string|null $userId = null,
    ) {
    }

    public function displayName(): string
    {
        return $this->studentNumber !== null && $this->studentNumber !== ''
            ? "{$this->fullName} ({$this->studentNumber})"
            : $this->fullName;
    }
}
