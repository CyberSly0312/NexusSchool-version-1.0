<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Services;

/**
 * Pure, dependency-free calculation engine for assessment statistics (SPR-009).
 *
 * Accepts normalized rows (`score` + `maximum`) so it never touches the
 * database and stays trivially unit-testable. The pass rate uses each row's
 * own passing score when provided, otherwise the configurable default passing
 * percentage of the maximum.
 *
 * @method static float average(array $rows)
 */
class AssessmentStatisticsCalculator
{
    /**
     * Average score across rows.
     *
     * @param  list<array{score: float, maximum: float}>  $rows
     */
    public function average(array $rows): float
    {
        if ($rows === []) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($rows as $row) {
            $total += $row['score'];
        }

        return (float) round($total / count($rows), $this->decimalPlaces());
    }

    /**
     * Minimum score across rows.
     *
     * @param  list<array{score: float, maximum: float}>  $rows
     */
    public function minimum(array $rows): float
    {
        if ($rows === []) {
            return 0.0;
        }

        return (float) round(min(array_column($rows, 'score')), $this->decimalPlaces());
    }

    /**
     * Maximum score across rows.
     *
     * @param  list<array{score: float, maximum: float}>  $rows
     */
    public function maximum(array $rows): float
    {
        if ($rows === []) {
            return 0.0;
        }

        return (float) round(max(array_column($rows, 'score')), $this->decimalPlaces());
    }

    /**
     * Median score across rows.
     *
     * @param  list<array{score: float, maximum: float}>  $rows
     */
    public function median(array $rows): float
    {
        if ($rows === []) {
            return 0.0;
        }

        $scores = array_values(array_column($rows, 'score'));
        sort($scores, SORT_NUMERIC);

        $middle = intdiv(count($scores), 2);

        if (count($scores) % 2 === 1) {
            return (float) round($scores[$middle], $this->decimalPlaces());
        }

        return (float) round(($scores[$middle - 1] + $scores[$middle]) / 2.0, $this->decimalPlaces());
    }

    /**
     * Share of rows whose score reaches the passing threshold. When a row does
     * not carry a passing score the configurable default passing percentage is
     * applied to its maximum.
     *
     * @param  list<array{score: float, maximum: float, passing?: float}>  $rows
     */
    public function passRate(array $rows): float
    {
        if ($rows === []) {
            return 0.0;
        }

        $passed = 0;

        foreach ($rows as $row) {
            if ($this->passes($row)) {
                $passed++;
            }
        }

        return (float) round(($passed / count($rows)) * 100.0, $this->decimalPlaces());
    }

    /**
     * @param  array{score: float, maximum: float, passing?: float}  $row
     */
    public function passes(array $row): bool
    {
        $maximum = $row['maximum'];

        if ($maximum <= 0.0) {
            return false;
        }

        $passing = $row['passing'] ?? ($maximum * $this->defaultPassingFraction());

        return $row['score'] >= $passing;
    }

    private function defaultPassingFraction(): float
    {
        return (float) config('nexus-assessment.statistics.default_passing_percentage', 50.0) / 100.0;
    }

    private function decimalPlaces(): int
    {
        return (int) config('nexus-assessment.statistics.decimal_places', 2);
    }
}
