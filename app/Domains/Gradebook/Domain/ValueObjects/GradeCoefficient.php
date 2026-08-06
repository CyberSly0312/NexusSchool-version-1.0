<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable coefficient of a subject inside a class (SPR-010). The
 * coefficient weights the subject average in the class/term general average.
 */
final readonly class GradeCoefficient
{
    private function __construct(
        public float $value,
    ) {
    }

    public static function fromValue(float $value): self
    {
        if (! is_finite($value) || $value <= 0.0) {
            throw new InvalidArgumentException("The coefficient must be a positive number, [{$value}] given.");
        }

        return new self((float) round($value, 2));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
