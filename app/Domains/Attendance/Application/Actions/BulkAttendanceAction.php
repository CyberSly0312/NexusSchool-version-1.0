<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\BulkAttendanceData;
use App\Domains\Attendance\Application\DTOs\BulkAttendanceItem;
use App\Domains\Attendance\Domain\Contracts\EnrollmentContract;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\Events\AttendanceRecorded;
use App\Domains\Attendance\Domain\Exceptions\SessionNotOpenException;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Bulk attendance write for a whole class (SPR-008).
 *
 * Performance: one query validates the class roster, one query filters already
 * recorded enrollments, and inserts are chunked with Eloquent's mass insert so
 * a 200-student class stays at a constant, small query count.
 */
final class BulkAttendanceAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
    ) {
    }

    /**
     * @return array{inserted: int, skipped: int, rejected: int}
     */
    public function execute(BulkAttendanceData $data, ?User $actor = null): array
    {
        if ($data->items === []) {
            return ['inserted' => 0, 'skipped' => 0, 'rejected' => 0];
        }

        return DB::transaction(function () use ($data, $actor): array {
            $session = AttendanceSession::query()->findOrFail($data->sessionId);

            if (! $session->isRecordable()) {
                throw SessionNotOpenException::forSession($session->getKey(), $session->status->value);
            }

            $session->assertMutable();

            $allowed = $this->allowedEnrollmentIds($session);
            $recorded = $this->recordedEnrollmentIds($session);

            $chunkSize = (int) config('nexus-attendance.bulk.chunk_size', 200);
            $inserted = 0;
            $skipped = 0;
            $rejected = 0;

            $rows = [];

            foreach ($data->items as $item) {
                if (! isset($allowed[$item->enrollmentId])) {
                    $rejected++;

                    continue;
                }

                if (isset($recorded[$item->enrollmentId])) {
                    $skipped++;

                    continue;
                }

                $rows[] = $this->toRow($session, $item, $actor);

                if (count($rows) >= $chunkSize) {
                    $inserted += $this->flush($session, $rows, $data, $actor);
                    $rows = [];
                }
            }

            if ($rows !== []) {
                $inserted += $this->flush($session, $rows, $data, $actor);
            }

            if ($inserted > 0) {
                $session->increment('recorded_students_count', $inserted);
            }

            return ['inserted' => $inserted, 'skipped' => $skipped, 'rejected' => $rejected];
        });
    }

    /**
     * @return array<int|string, true>
     */
    private function allowedEnrollmentIds(AttendanceSession $session): array
    {
        $allowed = [];

        foreach ($this->enrollment->enrollmentsOfClass($session->school_id, $session->school_class_id) as $enrollment) {
            $allowed[$enrollment->id] = true;
        }

        return $allowed;
    }

    /**
     * @return array<int|string, true>
     */
    private function recordedEnrollmentIds(AttendanceSession $session): array
    {
        $ids = AttendanceRecord::query()
            ->where('attendance_session_id', $session->getKey())
            ->pluck('enrollment_id')
            ->all();

        return array_fill_keys($ids, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function toRow(AttendanceSession $session, BulkAttendanceItem $item, ?User $actor): array
    {
        return [
            'uuid' => (string) Str::ulid(),
            'school_id' => $session->school_id,
            'attendance_session_id' => $session->getKey(),
            'enrollment_id' => $item->enrollmentId,
            'status' => $item->status,
            'check_in_time' => $item->checkInTime,
            'check_out_time' => $item->checkOutTime,
            'late_minutes' => $item->lateMinutes,
            'recorded_at' => now(),
            'recorded_by' => $actor?->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function flush(AttendanceSession $session, array $rows, BulkAttendanceData $data, ?User $actor): int
    {
        AttendanceRecord::query()->insert($rows);

        $enrollmentIds = array_values(array_unique(array_map(
            static fn (array $row) => $row['enrollment_id'],
            $rows,
        )));

        $records = AttendanceRecord::query()
            ->where('attendance_session_id', $session->getKey())
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->keyBy('enrollment_id');

        foreach ($rows as $row) {
            $status = AttendanceStatus::tryFrom((string) $row['status']);

            if ($status === null) {
                continue;
            }

            $record = $records->get($row['enrollment_id']);

            if ($record === null) {
                continue;
            }

            AttendanceRecorded::dispatch($session, $record, $row['enrollment_id'], $status, $actor);
        }

        return count($rows);
    }
}
