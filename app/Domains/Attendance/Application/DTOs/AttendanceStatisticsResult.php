<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

/**
 * Result of CalculateAttendanceStatisticsAction (SPR-008).
 */
final readonly class AttendanceStatisticsResult
{
    /**
     * @param  array<string, int>  $breakdown  status value => count
     */
    public function __construct(
        public int $sessionsCount,
        public float $attendancePercentage,
        public float $latePercentage,
        public int $consecutiveAbsences,
        public int $consecutiveLates,
        public float $riskScore,
        public float $trend,
        public array $breakdown,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sessions_count' => $this->sessionsCount,
            'attendance_percentage' => $this->attendancePercentage,
            'late_percentage' => $this->latePercentage,
            'consecutive_absences' => $this->consecutiveAbsences,
            'consecutive_lates' => $this->consecutiveLates,
            'risk_score' => $this->riskScore,
            'trend' => $this->trend,
            'breakdown' => $this->breakdown,
        ];
    }
}
