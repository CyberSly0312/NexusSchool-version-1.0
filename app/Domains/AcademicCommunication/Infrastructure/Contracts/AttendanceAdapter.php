<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract;
use App\Domains\AcademicCommunication\Domain\References\AttendanceSummaryReference;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

/**
 * Maps Attendance-domain models onto the AttendanceContract (SPR-012). The
 * engine renders attendance and conduct reports from validated attendance
 * aggregates; it never derives raw attendance itself.
 */
final class AttendanceAdapter implements AttendanceContract
{
    public function summaryForStudent(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string|null $termId = null,
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

        $query = AttendanceSummary::query()
            ->forSchool($schoolId)
            ->where('enrollment_id', $enrollment->getKey());

        if ($termId !== null) {
            $query->where('period_type', (string) $termId);
        }

        $summary = $query->orderByDesc('period_end')->first();

        if ($summary === null) {
            return null;
        }

        return new AttendanceSummaryReference(
            studentId: $studentId,
            expectedDays: $summary->sessions_count,
            presentDays: $summary->present_count,
            absentDays: $summary->absent_count,
            lateDays: $summary->late_count,
            attendanceRate: (float) $summary->attendance_percentage,
            periodLabel: sprintf('%s â†’ %s', $summary->period_start, $summary->period_end),
        );
    }
}
