<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Services;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePercentage;
use App\Domains\Attendance\Domain\ValueObjects\RiskScore;

/**
 * Weighted risk engine (SPR-008).
 *
 * The 0-100 score is the weighted sum of four normalized sub-scores:
 * attendance percentage (inverted), late percentage, consecutive absences and
 * consecutive lates. Weights are configurable through
 * config('nexus-attendance.risk.weights').
 */
class AttendanceRiskEngine
{
    /**
     * @param  array<string, float>  $weights
     */
    public function __construct(
        private array $weights = [],
    ) {
        $this->weights = $weights ?: $this->defaultWeights();
    }

    public function score(
        AttendancePercentage $percentage,
        float $latePercentage,
        int $consecutiveAbsences,
        int $consecutiveLates,
    ): RiskScore {
        $attendanceFactor = 1.0 - $percentage->ratio();
        $lateFactor = min(1.0, $latePercentage / 100.0);
        $absenceFactor = min(1.0, $consecutiveAbsences / 10.0);
        $lateStreakFactor = min(1.0, $consecutiveLates / 10.0);

        $score = ($this->weights['attendance_percentage'] * $attendanceFactor)
            + ($this->weights['late_percentage'] * $lateFactor)
            + ($this->weights['consecutive_absences'] * $absenceFactor)
            + ($this->weights['consecutive_lates'] * $lateStreakFactor);

        return new RiskScore((float) round(max(0.0, min(100.0, $score * 100.0)), 2));
    }

    /**
     * @return array<string, float>
     */
    private function defaultWeights(): array
    {
        $weights = (array) config('nexus-attendance.risk.weights', []);

        return [
            'attendance_percentage' => (float) ($weights['attendance_percentage'] ?? 0.55),
            'late_percentage' => (float) ($weights['late_percentage'] ?? 0.15),
            'consecutive_absences' => (float) ($weights['consecutive_absences'] ?? 0.20),
            'consecutive_lates' => (float) ($weights['consecutive_lates'] ?? 0.10),
        ];
    }
}
