<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\ReportCard\Domain\Contracts\AttendanceContract;
use App\Domains\ReportCard\Domain\References\AttendanceSummaryReference;

/**
 * AttendanceContract implementation (SPR-011). Projects the materialized
 * Attendance summaries onto the contract so the Report Card engine never
 * derives attendance itself.
 */
final class AttendanceAdapter implements AttendanceContract
{
    public function summaryForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?AttendanceSummaryReference {
        $enrollment = Enrollment::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->where('academic_calendar_year_id', $yearId)
            ->orderByDesc('id')
            ->first();

        if ($enrollment === null) {
            return null;
        }

        $summary = AttendanceSummary::query()
            ->forSchool($schoolId)
            ->where('enrollment_id', $enrollment->getKey())
            ->where('period_type', (string) $termId)
            ->orderByDesc('period_end')
            ->first();

        if ($summary === null) {
            return null;
        }

        return new AttendanceSummaryReference(
            expectedDays: (int) $summary->sessions_count,
            presentDays: (int) $summary->present_count,
            absentDays: (int) $summary->absent_count,
            lateDays: (int) $summary->late_count,
            attendanceRate: (float) $summary->attendance_percentage,
            periodLabel: sprintf('%s → %s', $summary->period_start, $summary->period_end),
        );
    }
}
