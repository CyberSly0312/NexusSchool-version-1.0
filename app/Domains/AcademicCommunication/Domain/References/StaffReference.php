<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of a staff member owned by the Staff domain (SPR-012).
 * Free of any concrete model.
 */
final readonly class StaffReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public ?string $staffNumber = null,
        public string $specialization = '',
        public ?string $email = null,
        public ?string $phone = null,
        public int|string|null $userId = null,
        public ?string $title = null,
    ) {
    }

    public function displayName(): string
    {
        return $this->staffNumber !== null && $this->staffNumber !== ''
            ? "{$this->fullName} ({$this->staffNumber})"
            : $this->fullName;
    }
}
