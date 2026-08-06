<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\References;

/**
 * Read-only projection of a school class owned by the Academic Framework
 * (SPR-008).
 */
final readonly class ClassReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
    ) {
    }
}
