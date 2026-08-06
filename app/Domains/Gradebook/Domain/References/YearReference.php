<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of an academic year owned by the Academic Framework
 * (SPR-010).
 */
final readonly class YearReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
        public string $status = '',
    ) {
    }
}
