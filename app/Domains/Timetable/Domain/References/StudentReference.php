<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\References;

/**
 * Read-only projection of a student owned by the Student identity domain.
 * Free of any concrete model.
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
}
