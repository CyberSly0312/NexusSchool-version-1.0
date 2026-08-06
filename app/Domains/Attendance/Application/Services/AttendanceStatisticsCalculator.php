<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Services;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\AttendancePercentage;

/**
 * Pure, dependency-free calculation engine for attendance statistics (SPR-008).
 *
 * Accepts normalized rows (`date` + `AttendanceStatus`) so it never touches the
 * database and stays trivially unit-testable. Percentage rules follow the
 * configurable classification: present/late-equivalent count towards the
 * numerator, absent-equivalent towards the denominator, neutral rows are
 * excluded entirely.
 *
 * @method static AttendancePercentage percentage(array $rows)
 */
class AttendanceStatisticsCalculator
{
    /**
     * Attendance percentage over a set of rows.
     *
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     */
    public function percentage(array $rows): AttendancePercentage
    {
        $present = 0;
        $denominator = 0;

        foreach ($rows as $row) {
            $status = $row['status'];

            if ($status->isNeutral()) {
                continue;
            }

            $denominator++;

            if ($status->countsAsPresent()) {
                $present++;
            }
        }

        return AttendancePercentage::fromCounts($present, $denominator);
    }

    /**
     * Share of records marked late among all present-equivalent records.
     *
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     */
    public function latePercentage(array $rows): float
    {
        $late = 0;
        $presentEquivalents = 0;

        foreach ($rows as $row) {
            $status = $row['status'];

            if (! $status->countsAsPresent()) {
                continue;
            }

            $presentEquivalents++;

            if ($status === AttendanceStatus::Late) {
                $late++;
            }
        }

        if ($presentEquivalents === 0) {
            return 0.0;
        }

        return (float) round(($late / $presentEquivalents) * 100.0, 2);
    }

    /**
     * Current trailing streak of consecutive absences (absent-equivalent).
     *
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     */
    public function consecutiveAbsences(array $rows): int
    {
        $streak = 0;

        foreach (array_reverse($rows) as $row) {
            if ($row['status']->countsAsAbsent()) {
                $streak++;
                continue;
            }

            if ($row['status']->isNeutral()) {
                continue;
            }

            break;
        }

        return $streak;
    }

    /**
     * Current trailing streak of consecutive lates.
     *
     * @param  list<array{date: string, status: AttendanceStatus}>  $rows
     */
    public function consecutiveLates(array $rows): int
    {
        $streak = 0;

        foreach (array_reverse($rows) as $row) {
            if ($row['status'] === AttendanceStatus::Late) {
                $streak++;
                continue;
            }

            if ($row['status']->isNeutral()) {
                continue;
            }

            break;
        }

        return $streak;
    }

    /**
     * Trend in percentage points: current minus previous. Positive means
     * improvement.
     *
     * @param  list<array{date: string, status: AttendanceStatus}>  $current
     * @param  list<array{date: string, status: AttendanceStatus}>  $previous
     */
    public function trend(array $current, array $previous): float
    {
        return (float) round(
            $this->percentage($current)->value - $this->percentage($previous)->value,
            2,
        );
    }
}
