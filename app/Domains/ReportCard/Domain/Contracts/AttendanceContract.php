<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\AttendanceSummaryReference;

/**
 * Read-only gateway to the Attendance engine (SPR-011).
 */
interface AttendanceContract
{
    public function summaryForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?AttendanceSummaryReference;
}
