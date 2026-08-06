<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

/**
 * Identity of the student a council decision is taken for (SPR-013).
 */
final readonly class StudentReference
{
    public function __construct(
        public int|string $id,
        public string $fullName,
        public string $studentNumber,
        public int|string|null $userId = null,
    ) {
    }
}
