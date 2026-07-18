<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\DTOs\AnalyticsSnapshotData;
use App\Models\AnalyticsSnapshot;
use App\Models\Kpi;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(
        protected Kpi $kpiModel = new Kpi(),
        protected AnalyticsSnapshot $snapshotModel = new AnalyticsSnapshot(),
    ) {
    }

    public function getEnrollmentStats(string $schoolId): array
    {
        return [
            'total' => DB::table('student_enrollments')->where('school_id', $schoolId)->count(),
            'active' => DB::table('student_enrollments')->where('school_id', $schoolId)->where('status', 'active')->count(),
            'completed' => DB::table('student_enrollments')->where('school_id', $schoolId)->where('status', 'completed')->count(),
            'withdrawn' => DB::table('student_enrollments')->where('school_id', $schoolId)->where('status', 'withdrawn')->count(),
        ];
    }

    public function getAttendanceStats(string $schoolId): array
    {
        return [
            'total_records' => DB::table('attendance_records')->where('school_id', $schoolId)->count(),
            'present' => DB::table('attendance_records')->where('school_id', $schoolId)->where('status', 'present')->count(),
            'absent' => DB::table('attendance_records')->where('school_id', $schoolId)->where('status', 'absent')->count(),
            'late' => DB::table('attendance_records')->where('school_id', $schoolId)->where('status', 'late')->count(),
        ];
    }

    public function getLibraryStats(string $schoolId): array
    {
        return [
            'total_books' => DB::table('books')->where('school_id', $schoolId)->count(),
            'active_loans' => DB::table('book_loans')->where('school_id', $schoolId)->where('status', 'active')->count(),
            'overdue_loans' => DB::table('book_loans')->where('school_id', $schoolId)
                ->where('status', 'active')->where('due_date', '<', now())->count(),
            'total_fines' => DB::table('fines')->where('school_id', $schoolId)->where('status', '!=', 'paid')->sum('amount'),
        ];
    }

    public function getTransportStats(string $schoolId): array
    {
        return [
            'total_vehicles' => DB::table('vehicles')->where('school_id', $schoolId)->count(),
            'active_vehicles' => DB::table('vehicles')->where('school_id', $schoolId)->where('status', 'active')->count(),
            'total_routes' => DB::table('routes')->where('school_id', $schoolId)->count(),
            'total_trips_today' => DB::table('trips')->where('school_id', $schoolId)
                ->whereDate('trip_date', now()->toDateString())->count(),
        ];
    }

    public function getCommunicationStats(string $schoolId): array
    {
        return [
            'total_notifications' => DB::table('notifications')->where('school_id', $schoolId)->count(),
            'unread_notifications' => DB::table('notifications')->where('school_id', $schoolId)->whereNull('read_at')->count(),
            'total_announcements' => DB::table('announcements')->where('school_id', $schoolId)->count(),
            'total_messages' => DB::table('messages')->where('school_id', $schoolId)->count(),
        ];
    }

    public function createSnapshot(string $schoolId, string $type, array $data, ?string $periodStart = null, ?string $periodEnd = null): AnalyticsSnapshotData
    {
        $snapshot = $this->snapshotModel->create([
            'school_id' => $schoolId,
            'snapshot_type' => $type,
            'data' => $data,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'generated_at' => now(),
            'is_immutable' => true,
        ]);

        return AnalyticsSnapshotData::create($snapshot->toArray());
    }

    public function getSnapshots(string $schoolId, ?string $type = null, ?string $periodStart = null, ?string $periodEnd = null)
    {
        $query = $this->snapshotModel->where('school_id', $schoolId);

        if ($type) {
            $query->where('snapshot_type', $type);
        }

        if ($periodStart) {
            $query->where('period_start', '>=', $periodStart);
        }

        if ($periodEnd) {
            $query->where('period_end', '<=', $periodEnd);
        }

        return $query->orderBy('generated_at', 'desc')->get();
    }
}
