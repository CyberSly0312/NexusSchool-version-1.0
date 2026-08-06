<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\AttendanceSummaryReference;

/**
 * Read-only gateway to the Attendance engine (SPR-012). Attendance reports and
 * conduct reports are rendered here from validated attendance aggregates.
 */
interface AttendanceContract
{
    public function summaryForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string|null $termId = null,
    ): ?AttendanceSummaryReference;
}
