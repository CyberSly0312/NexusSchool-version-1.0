<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\References;

/**
 * Read-only projection of an academic term owned by the Academic Framework
 * (SPR-010). The term bounds the assessment scores a gradebook aggregates.
 */
final readonly class TermReference
{
    public function __construct(
        public int|string $id,
        public string $name,
        public ?string $code = null,
        public ?string $startsOn = null,
        public ?string $endsOn = null,
        public string $status = '',
    ) {
    }
}
