<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * Read-only projection of an academic calendar year owned by the Academic
 * Framework (SPR-009).
 */
final readonly class YearReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code,
        public string $startDate,
        public string $endDate,
        public bool $isActive = false,
    ) {
    }
}
