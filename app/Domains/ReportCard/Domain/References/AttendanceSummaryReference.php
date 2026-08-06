<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * Attendance summary rendered on a bulletin (SPR-011). Supplied by the
 * Attendance engine through the contract.
 */
final readonly class AttendanceSummaryReference
{
    public function __construct(
        public int $expectedDays = 0,
        public int $presentDays = 0,
        public int $absentDays = 0,
        public int $lateDays = 0,
        public ?float $attendanceRate = null,
        public ?string $periodLabel = null,
    ) {
    }
}
