<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of an academic subject owned by the Academic Framework
 * (SPR-010). A gradebook is scoped to one subject.
 */
final readonly class SubjectReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
    ) {
    }
}
