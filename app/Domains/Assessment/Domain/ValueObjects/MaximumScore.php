<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable maximum achievable score of an assessment (SPR-009). Must be a
 * finite, strictly positive number so percentage computation never divides
 * by zero.
 */
final readonly class MaximumScore
{
    private function __construct(
        public float $value,
    ) {
    }

    public static function fromFloat(float $value): self
    {
        if (! is_finite($value)) {
            throw new InvalidArgumentException('The maximum score must be a finite number.');
        }

        if ($value <= 0.0) {
            throw new InvalidArgumentException('The maximum score must be greater than zero.');
        }

        return new self((float) round($value, 2));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
