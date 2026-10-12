<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\References;

/**
 * An academic policy consumed for a statistics default (SPR-009, supplied by
 * the Academic Policies engine).
 */
final readonly class PolicyReference
{
    public function __construct(
        public string $key,
        public string $label,
        public mixed $value,
        public ?string $valueType = null,
        public ?string $description = null,
    ) {
    }
}
