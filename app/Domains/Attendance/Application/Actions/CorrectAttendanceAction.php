<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Application\DTOs\CorrectionData;
use App\Domains\Attendance\Domain\Events\AttendanceCorrected;
use App\Domains\Attendance\Domain\Exceptions\AttendanceException;
use App\Domains\Attendance\Domain\Exceptions\SessionLockedException;
use App\Domains\Attendance\Domain\Exceptions\UnauthorizedCorrectionException;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Authorized correction of an attendance record (SPR-008).
 *
 * A correction never overwrites history: it appends an AttendanceCorrection
 * audit entry carrying the previous and new status, then updates the record.
 * Corrections require explicit authorization (the `attendance.correct`
 * capability is enforced at the policy layer; here authorization is the
 * designated authorizer identity).
 */
final class CorrectAttendanceAction
{
    public function execute(CorrectionData $data, ?User $actor = null): AttendanceCorrection
    {
        if ($data->authorizedBy === null) {
            throw UnauthorizedCorrectionException::forRecord($data->recordId);
        }

        return DB::transaction(function () use ($data, $actor): AttendanceCorrection {
            $record = AttendanceRecord::query()->with('session')->findOrFail($data->recordId);

            if ($record->session?->isArchived()) {
                throw SessionLockedException::forSession($record->session->getKey());
            }

            if ($record->status === $data->newStatus) {
                throw new AttendanceException('Correction would not change the attendance status.');
            }

            $previous = $record->status;

            $correction = AttendanceCorrection::create([
                'school_id' => $record->school_id,
                'attendance_record_id' => $record->getKey(),
                'previous_status' => $previous->value,
                'new_status' => $data->newStatus->value,
                'reason_type' => $data->reason->type,
                'reason_text' => $data->reason->text !== '' ? $data->reason->text : null,
                'is_authorized' => true,
                'requested_by' => $actor?->getKey(),
                'authorized_by' => $data->authorizedBy,
                'authorized_at' => now(),
                'corrected_at' => now(),
                'notes' => $data->notes,
            ]);

            $record->status = $data->newStatus;
            $record->save();

            AttendanceCorrected::dispatch(
                record: $record,
                previousStatus: $previous,
                newStatus: $data->newStatus,
                correctionId: $correction->getKey(),
                reasonType: $data->reason->type,
                actor: $actor,
            );

            return $correction;
        });
    }
}
