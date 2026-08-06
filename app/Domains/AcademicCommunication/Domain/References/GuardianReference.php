<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Read-only projection of a guardian owned by the Guardian domain (SPR-012).
 * Free of any concrete model.
 */
final readonly class GuardianReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public ?string $guardianNumber = null,
        public ?string $email = null,
        public ?string $phone = null,
        public bool $isPrimary = false,
        public int|string|null $userId = null,
    ) {
    }
}
