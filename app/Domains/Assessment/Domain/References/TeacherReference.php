<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * Read-only projection of a teacher (a Staff specialization) owned by the Staff
 * domain (SPR-009). The subjectIds list backs the "teachers may only score
 * assigned subjects" rule.
 */
final readonly class TeacherReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public string $firstName,
        public string $lastName,
        /** @var list<int|string> */
        public array $subjectIds = [],
        public bool $isActive = true,
    ) {
    }
}
