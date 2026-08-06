<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of a staff member owned by the Staff domain (SPR-010).
 * The teacher owning a gradebook is the one who recorded the assessment
 * scores the gradebook aggregates.
 */
final readonly class TeacherReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public ?string $email = null,
        public ?string $subject = null,
    ) {
    }
}
