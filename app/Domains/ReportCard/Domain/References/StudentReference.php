<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * Identity of the student a bulletin is composed for (SPR-011).
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
