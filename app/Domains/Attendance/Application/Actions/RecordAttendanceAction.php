<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\RecordAttendanceData;
use App\Domains\Attendance\Domain\Contracts\EnrollmentContract;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\Events\AttendanceRecorded;
use App\Domains\Attendance\Domain\Exceptions\DuplicateRecordException;
use App\Domains\Attendance\Domain\Exceptions\SessionNotOpenException;
use App\Domains\Attendance\Domain\Exceptions\StudentNotEnrolledException;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Attendance\Infrastructure\Models\EarlyDeparture;
use App\Domains\Attendance\Infrastructure\Models\LateArrival;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Records a single student's attendance for an open session (SPR-008).
 *
 * The student is resolved through their enrollment, never directly; a student
 * may only appear once per session.
 */
final class RecordAttendanceAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
    ) {
    }

    public function execute(RecordAttendanceData $data, ?User $actor = null): AttendanceRecord
    {
        return DB::transaction(function () use ($data, $actor): AttendanceRecord {
            $session = AttendanceSession::query()->findOrFail($data->sessionId);

            if (! $session->isRecordable()) {
                throw SessionNotOpenException::forSession($session->getKey(), $session->status->value);
            }

            $session->assertMutable();

            $this->assertEnrollmentInClass($session, $data->enrollmentId);
            $this->assertNotDuplicate($session, $data->enrollmentId);

            $record = AttendanceRecord::create([
                'school_id' => $session->school_id,
                'attendance_session_id' => $session->getKey(),
                'enrollment_id' => $data->enrollmentId,
                'status' => $data->status->value,
                'check_in_time' => $data->checkInTime,
                'check_out_time' => $data->checkOutTime,
                'late_minutes' => $this->resolveLateMinutes($data),
                'recorded_at' => now(),
                'notes' => $data->notes,
                'recorded_by' => $actor?->getKey(),
            ]);

            $this->recordLateArrival($session, $record, $data, $actor);
            $this->recordEarlyDeparture($session, $record, $data, $actor);

            $session->increment('recorded_students_count');

            AttendanceRecorded::dispatch($session->refresh(), $record, $data->enrollmentId, $data->status, $actor);

            return $record;
        });
    }

    private function assertEnrollmentInClass(AttendanceSession $session, int|string $enrollmentId): void
    {
        $enrollment = $this->enrollment->enrollment($session->school_id, $enrollmentId);

        if ($enrollment === null || ! $enrollment->isActive() || $enrollment->classId !== $session->school_class_id) {
            throw StudentNotEnrolledException::forStudent($enrollmentId, $session->school_class_id);
        }
    }

    private function assertNotDuplicate(AttendanceSession $session, int|string $enrollmentId): void
    {
        $exists = AttendanceRecord::query()
            ->where('attendance_session_id', $session->getKey())
            ->where('enrollment_id', $enrollmentId)
            ->exists();

        if ($exists) {
            throw DuplicateRecordException::forEnrollment($session->getKey(), $enrollmentId);
        }
    }

    private function resolveLateMinutes(RecordAttendanceData $data): ?int
    {
        if ($data->lateMinutes !== null) {
            return max(0, $data->lateMinutes);
        }

        if ($data->status === AttendanceStatus::Late && $data->checkInTime !== null) {
            return 0;
        }

        return null;
    }

    private function recordLateArrival(AttendanceSession $session, AttendanceRecord $record, RecordAttendanceData $data, ?User $actor): void
    {
        if ($data->status !== AttendanceStatus::Late || $data->checkInTime === null) {
            return;
        }

        $minutes = $data->lateMinutes ?? 0;

        LateArrival::create([
            'school_id' => $session->school_id,
            'attendance_record_id' => $record->getKey(),
            'minutes' => $minutes,
            'arrived_at' => $data->checkInTime,
            'recorded_by' => $actor?->getKey(),
        ]);
    }

    private function recordEarlyDeparture(AttendanceSession $session, AttendanceRecord $record, RecordAttendanceData $data, ?User $actor): void
    {
        if ($data->checkOutTime === null) {
            return;
        }

        EarlyDeparture::create([
            'school_id' => $session->school_id,
            'attendance_record_id' => $record->getKey(),
            'minutes' => 0,
            'departed_at' => $data->checkOutTime,
            'recorded_by' => $actor?->getKey(),
        ]);
    }
}
