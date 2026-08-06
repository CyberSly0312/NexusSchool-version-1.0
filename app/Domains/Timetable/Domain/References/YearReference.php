<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\References;

/**
 * Read-only projection of an academic calendar year owned by the Academic
 * Framework. Deliberately free of any concrete model.
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
