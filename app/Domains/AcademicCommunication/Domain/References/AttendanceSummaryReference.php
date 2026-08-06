<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * Attendance aggregate consumed from the Attendance engine (SPR-012) for
 * attendance reports and conduct reports.
 */
final readonly class AttendanceSummaryReference
{
    public function __construct(
        public int|string $studentId,
        public int $expectedDays,
        public int $presentDays,
        public int $absentDays,
        public int $lateDays,
        public float $attendanceRate,
        public ?string $periodLabel = null,
    ) {
    }
}
