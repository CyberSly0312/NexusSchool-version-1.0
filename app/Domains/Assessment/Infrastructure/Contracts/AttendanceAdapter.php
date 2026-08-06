<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Contracts\AttendanceContract;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;

/**
 * Maps Attendance-domain models onto the AttendanceContract. This is the only
 * place in the Assessment domain allowed to see Attendance models.
 *
 * Present-equivalent classification follows the Attendance engine's own
 * configurable statuses (counts_as_present).
 */
final class AttendanceAdapter implements AttendanceContract
{
    public function isPresent(string $schoolId, int|string $enrollmentId, string $date): bool
    {
        $presentStatuses = $this->presentStatuses();

        if ($presentStatuses === []) {
            return false;
        }

        return $this->query($schoolId, $enrollmentId, $presentStatuses)
            ->where('attendance_sessions.date', $date)
            ->exists();
    }

    public function presentDates(string $schoolId, int|string $enrollmentId, string $fromDate, string $toDate): array
    {
        $presentStatuses = $this->presentStatuses();

        if ($presentStatuses === []) {
            return [];
        }

        return $this->query($schoolId, $enrollmentId, $presentStatuses)
            ->whereBetween('attendance_sessions.date', [$fromDate, $toDate])
            ->distinct()
            ->orderBy('attendance_sessions.date')
            ->pluck('attendance_sessions.date')
            ->map(fn (string $date): string => (string) $date)
            ->all();
    }

    /**
     * @param  list<string>  $presentStatuses
     */
    private function query(string $schoolId, int|string $enrollmentId, array $presentStatuses): \Illuminate\Database\Eloquent\Builder
    {
        return AttendanceRecord::query()
            ->join('attendance_sessions', 'attendance_sessions.id', '=', 'attendance_session_records.attendance_session_id')
            ->where('attendance_session_records.school_id', $schoolId)
            ->where('attendance_session_records.enrollment_id', $enrollmentId)
            ->whereIn('attendance_session_records.status', $presentStatuses);
    }

    /**
     * @return list<string>
     */
    private function presentStatuses(): array
    {
        return array_values(array_map(
            static fn (AttendanceStatus $status): string => $status->value,
            array_filter(
                AttendanceStatus::cases(),
                static fn (AttendanceStatus $status): bool => $status->countsAsPresent(),
            ),
        ));
    }
}
