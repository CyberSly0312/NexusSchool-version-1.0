<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\References;

/**
 * Read-only projection of a teacher (a Staff specialization) owned by the Staff
 * domain (SPR-008).
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
