<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\ValueObjects;

/**
 * Immutable percentage in the [0, 100] range with configurable rounding
 * (SPR-008).
 */
final readonly class AttendancePercentage
{
    private function __construct(
        public float $value,
        public int $presentCount,
        public int $denominator,
    ) {
    }

    public static function fromCounts(int $presentCount, int $denominator): self
    {
        if ($denominator < 0 || $presentCount < 0) {
            throw new \InvalidArgumentException('Counts must be non-negative.');
        }

        if ($denominator === 0) {
            return new self(0.0, $presentCount, 0);
        }

        $decimalPlaces = (int) config('nexus-attendance.percentage.decimal_places', 2);

        return new self(
            (float) round(($presentCount / $denominator) * 100.0, $decimalPlaces),
            $presentCount,
            $denominator,
        );
    }

    public static function fromFloat(float $value, int $decimalPlaces = null): self
    {
        if ($decimalPlaces === null) {
            $decimalPlaces = (int) config('nexus-attendance.percentage.decimal_places', 2);
        }

        $clamped = max(0.0, min(100.0, $value));

        return new self(
            (float) round($clamped, $decimalPlaces),
            (int) round($clamped),
            100,
        );
    }

    public function ratio(): float
    {
        return $this->denominator === 0 ? 0.0 : $this->value / 100.0;
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
