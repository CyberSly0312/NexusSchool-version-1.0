<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\ValueObjects;

/**
 * Student-level grade point average (SPR-010).
 *
 * The GPA is the coefficient-weighted mean of the grade points of the student's
 * published grade entries, expressed on the school GPA scale (default 4.0).
 * The same value object carries the CGPA: a CGPA is simply the GPA aggregated
 * over every published entry of the student, with no period filter.
 */
final readonly class Gpa
{
    /**
     * @param  array<float>  $points
     * @param  array<float>  $coefficients
     */
    public static function weighted(
        array $points,
        array $coefficients = [],
        float $scale = 4.0,
        int $decimalPlaces = 2,
    ): ?self {
        if ($points === []) {
            return null;
        }

        $weighted = 0.0;
        $totalCoefficient = 0.0;

        foreach ($points as $index => $point) {
            $coefficient = max(0.0, (float) ($coefficients[$index] ?? 1.0));
            $weighted += (float) $point * $coefficient;
            $totalCoefficient += $coefficient;
        }

        $gpa = $totalCoefficient > 0.0
            ? $weighted / $totalCoefficient
            : array_sum($points) / count($points);

        return new self(
            gpa: round($gpa, $decimalPlaces),
            scale: $scale,
            gradeCount: count($points),
            totalCoefficient: round($totalCoefficient, $decimalPlaces),
        );
    }

    public function __construct(
        public float $gpa,
        public float $scale = 4.0,
        public int $gradeCount = 0,
        public float $totalCoefficient = 0.0,
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->gradeCount === 0;
    }
}
