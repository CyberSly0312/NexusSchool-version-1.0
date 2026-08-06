<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable assessment weight expressed as a percentage of the term total
 * (SPR-009). A weight must stay inside [0, max_total]; the ceiling comes from
 * the school weight policy.
 */
final readonly class Weight
{
    private function __construct(
        public float $value,
    ) {
    }

    public static function fromPercent(float $value): self
    {
        if (! is_finite($value)) {
            throw new InvalidArgumentException('The weight must be a finite number.');
        }

        $maxTotal = (float) config('nexus-assessment.weights.max_total', 100.0);

        if ($value < 0.0 || $value > $maxTotal) {
            throw new InvalidArgumentException(sprintf('The weight must be between 0 and %s.', $maxTotal));
        }

        $decimalPlaces = (int) config('nexus-assessment.weights.decimal_places', 2);

        return new self((float) round($value, $decimalPlaces));
    }

    /**
     * The weight as a fraction of 1.0 (used for weighted aggregates).
     */
    public function fraction(): float
    {
        return $this->value / 100.0;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
