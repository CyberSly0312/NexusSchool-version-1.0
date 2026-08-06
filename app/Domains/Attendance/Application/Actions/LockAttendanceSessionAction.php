<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Events\AttendanceLocked;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;

/**
 * Locks a session. A locked session is immutable: no further records can be
 * added and direct edits are refused; the only sanctioned change afterwards is
 * an authorized correction (SPR-008).
 */
final class LockAttendanceSessionAction
{
    public function execute(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        $session->assertCanTransitionTo(AttendanceSessionStatus::Locked);

        $session->status = AttendanceSessionStatus::Locked;
        $session->locked_at = now();
        $session->save();

        AttendanceLocked::dispatch($session->refresh(), $actor);

        return $session;
    }
}
