<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * Read-only projection of a class section owned by the Academic Framework
 * (SPR-009).
 */
final readonly class SectionReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
        public int|string|null $classId = null,
    ) {
    }
}
