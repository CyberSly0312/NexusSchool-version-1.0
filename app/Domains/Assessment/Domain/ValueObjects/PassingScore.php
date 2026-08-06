<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable passing score of an assessment (SPR-009). Must be a non-negative
 * number that never exceeds the maximum achievable score.
 */
final readonly class PassingScore
{
    private function __construct(
        public float $value,
    ) {
    }

    public static function fromFloat(float $value, ?float $maximum = null): self
    {
        if (! is_finite($value)) {
            throw new InvalidArgumentException('The passing score must be a finite number.');
        }

        if ($value < 0.0) {
            throw new InvalidArgumentException('The passing score must not be negative.');
        }

        if ($maximum !== null && $value > $maximum) {
            throw new InvalidArgumentException('The passing score must not exceed the maximum score.');
        }

        return new self((float) round($value, 2));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
