<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Services;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use Carbon\CarbonImmutable;

/**
 * Read-only query for attendance records grouped by date. Centralized so
 * statistics and summary actions share one efficient, join-based query and
 * never trigger N+1.
 */
class AttendanceRecordQuery
{
    /**
     * Normalized rows (`date` + `status`) for one enrollment inside a window,
     * ordered chronologically.
     *
     * @return list<array{date: string, status: AttendanceStatus}>
     */
    public function rowsForEnrollment(
        string $schoolId,
        int|string $enrollmentId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): array {
        return AttendanceRecord::query()
            ->where('attendance_session_records.school_id', $schoolId)
            ->join('attendance_sessions', 'attendance_session_records.attendance_session_id', '=', 'attendance_sessions.id')
            ->where('attendance_session_records.enrollment_id', $enrollmentId)
            ->whereBetween('attendance_sessions.date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('attendance_sessions.date')
            ->orderBy('attendance_sessions.period_number')
            ->select('attendance_session_records.*', 'attendance_sessions.date as session_date')
            ->get()
            ->map(fn (AttendanceRecord $record): array => [
                'date' => (string) $record->session_date,
                'status' => $record->status,
            ])
            ->all();
    }
}
