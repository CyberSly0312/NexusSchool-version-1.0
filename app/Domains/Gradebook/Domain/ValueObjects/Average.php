<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\ValueObjects;

/**
 * Immutable average projected onto the school grading scale (SPR-010).
 *
 * A percentage is rendered as `percentage / 100 * scale`, i.e. a value on a
 * 20-point scale (secondary) or a 100-point scale (primary).
 */
final readonly class Average
{
    private function __construct(
        public float $value,
        public int $scale,
    ) {
    }

    public static function fromPercentage(float $percentage, int $scale): self
    {
        if ($scale <= 0) {
            $scale = 20;
        }

        $decimalPlaces = (int) config('nexus-gradebook.grade_system.decimal_places', 2);

        return new self((float) round(($percentage / 100.0) * $scale, $decimalPlaces), $scale);
    }

    public function ratio(): float
    {
        return $this->value / $this->scale;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
