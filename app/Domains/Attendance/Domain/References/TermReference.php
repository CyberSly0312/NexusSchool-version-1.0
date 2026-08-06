<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\References;

/**
 * Read-only projection of an academic calendar term owned by the Academic
 * Framework (SPR-008).
 */
final readonly class TermReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code,
        public string $startsOn,
        public string $endsOn,
        public bool $isOpen = false,
    ) {
    }
}
