<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of a class section owned by the Academic Framework
 * (SPR-010). Gradebooks are usually computed per class; an optional section
 * narrows the scope to a single stream.
 */
final readonly class SectionReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public int|string|null $classId = null,
    ) {
    }
}
