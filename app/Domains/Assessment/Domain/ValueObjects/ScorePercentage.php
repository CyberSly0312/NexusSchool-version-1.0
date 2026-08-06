<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable percentage score in the [0, 100] range (SPR-009).
 *
 * The value is a ratio (score / maximum) expressed as a percentage with the
 * configurable rounding of the nexus-assessment configuration.
 */
final readonly class ScorePercentage
{
    private function __construct(
        public float $value,
    ) {
    }

    /**
     * @param  float  $score  raw score obtained
     * @param  float  $maximum  maximum achievable score (must be > 0)
     */
    public static function fromScore(float $score, float $maximum): self
    {
        if ($maximum <= 0.0) {
            throw new InvalidArgumentException('The maximum score must be greater than zero.');
        }

        $decimalPlaces = (int) config('nexus-assessment.validation.decimal_places', 2);

        $clamped = max(0.0, min(100.0, ($score / $maximum) * 100.0));

        return new self((float) round($clamped, $decimalPlaces));
    }

    public function ratio(): float
    {
        return $this->value / 100.0;
    }

    public function isBelow(float $threshold): bool
    {
        return $this->value < $threshold;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
