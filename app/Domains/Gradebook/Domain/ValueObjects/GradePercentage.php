<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\ValueObjects;

use App\Domains\Gradebook\Domain\Exceptions\InvalidPercentageException;

/**
 * Immutable term percentage in the [0, 100] range (SPR-010).
 *
 * The percentage is the canonical value of the grade engine; every other
 * representation (average on the school scale, letter grade, grade point,
 * appreciation) derives from it.
 */
final readonly class GradePercentage
{
    private function __construct(
        public float $value,
    ) {
    }

    public static function fromValue(float $value): self
    {
        if (! is_finite($value) || $value < 0.0 || $value > 100.0) {
            throw InvalidPercentageException::for($value);
        }

        $decimalPlaces = (int) config('nexus-gradebook.grade_system.decimal_places', 2);

        return new self((float) round($value, $decimalPlaces));
    }

    /**
     * Projects the percentage onto the configured school scale (20 or 100).
     */
    public function toScale(int $scale): Average
    {
        return Average::fromPercentage($this->value, $scale);
    }

    public function isPassing(float $threshold = 50.0): bool
    {
        return $this->value >= $threshold;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
