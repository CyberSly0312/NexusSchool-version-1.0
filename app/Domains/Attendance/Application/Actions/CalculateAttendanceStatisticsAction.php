<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\AttendanceStatisticsResult;
use App\Domains\Attendance\Application\DTOs\StatisticsData;
use App\Domains\Attendance\Application\Services\AttendanceRecordQuery;
use App\Domains\Attendance\Application\Services\AttendanceRiskEngine;
use App\Domains\Attendance\Application\Services\AttendanceStatisticsCalculator;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;

/**
 * Computes attendance statistics for one enrollment over one period (SPR-008).
 * Pure computation over read-only rows; it never persists anything.
 */
final class CalculateAttendanceStatisticsAction
{
    public function __construct(
        private readonly AttendanceRecordQuery $records,
        private readonly AttendanceStatisticsCalculator $calculator,
        private readonly AttendanceRiskEngine $risk,
    ) {
    }

    public function execute(StatisticsData $data): AttendanceStatisticsResult
    {
        $rows = $this->records->rowsForEnrollment(
            $data->schoolId,
            $data->enrollmentId,
            $data->period->startsOn,
            $data->period->endsOn,
        );

        $percentage = $this->calculator->percentage($rows);
        $latePercentage = $this->calculator->latePercentage($rows);
        $consecutiveAbsences = $this->calculator->consecutiveAbsences($rows);
        $consecutiveLates = $this->calculator->consecutiveLates($rows);
        $trend = $this->trend($data, $rows);

        $riskScore = $this->risk->score(
            $percentage,
            $latePercentage,
            $consecutiveAbsences,
            $consecutiveLates,
        );

        return new AttendanceStatisticsResult(
            sessionsCount: count($rows),
            attendancePercentage: $percentage->value,
            latePercentage: $latePercentage,
            consecutiveAbsences: $consecutiveAbsences,
            consecutiveLates: $consecutiveLates,
            riskScore: $riskScore->value,
            trend: $trend,
            breakdown: $this->breakdown($rows),
        );
    }

    /**
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     */
    private function trend(StatisticsData $data, array $rows): float
    {
        $length = (int) $data->period->endsOn->diffInDays($data->period->startsOn) + 1;
        $previousEnd = $data->period->startsOn->subDay();

        if ($length <= 1) {
            return 0.0;
        }

        $previousStart = $data->period->startsOn->subDays($length);

        $previousRows = $this->records->rowsForEnrollment(
            $data->schoolId,
            $data->enrollmentId,
            $previousStart,
            $previousEnd,
        );

        return $this->calculator->trend($rows, $previousRows);
    }

    /**
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     *
     * @return array<string, int>
     */
    private function breakdown(array $rows): array
    {
        $breakdown = [];

        foreach (AttendanceStatus::cases() as $status) {
            $breakdown[$status->value] = 0;
        }

        foreach ($rows as $row) {
            $breakdown[$row['status']->value]++;
        }

        return $breakdown;
    }
}
