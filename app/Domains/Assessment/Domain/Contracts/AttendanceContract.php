<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

/**
 * Read-only gateway to the Attendance domain (SPR-009).
 *
 * Used by behavior assessments and score validation to know whether a student
 * was actually present the day an assessment was administered.
 */
interface AttendanceContract
{
    /**
     * Whether the enrollment has an attendance record classified as present
     * (or present-equivalent) on the given date.
     */
    public function isPresent(string $schoolId, int|string $enrollmentId, string $date): bool;

    /**
     * Dates on which the enrollment holds a present-equivalent attendance record.
     *
     * @return list<string>  dates as Y-m-d
     */
    public function presentDates(string $schoolId, int|string $enrollmentId, string $fromDate, string $toDate): array;
}
