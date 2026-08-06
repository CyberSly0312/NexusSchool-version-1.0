<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of a student owned by the Student identity domain.
 * Free of any concrete model (SPR-010).
 */
final readonly class StudentReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public ?string $studentNumber = null,
        public string $firstName = '',
        public string $lastName = '',
    ) {
    }

    public function displayName(): string
    {
        return $this->studentNumber !== null && $this->studentNumber !== ''
            ? "{$this->fullName} ({$this->studentNumber})"
            : $this->fullName;
    }
}
