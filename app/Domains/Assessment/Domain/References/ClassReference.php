<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * Read-only projection of a school class owned by the Academic Framework
 * (SPR-009). Scores are bound to the enrollments of this class.
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
