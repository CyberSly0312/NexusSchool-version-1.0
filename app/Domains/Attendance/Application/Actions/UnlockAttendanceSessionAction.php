<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Events\AttendanceUnlocked;
use App\Domains\Attendance\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;

/**
 * Reopens a locked session under a controlled reverse transition (SPR-008).
 *
 * Unlocking is a sanctioned, audited exception to the forward-only lifecycle:
 * it always leaves an audit trail through AttendanceUnlocked and requires the
 * dedicated `attendance.unlock` capability at the policy layer.
 */
final class UnlockAttendanceSessionAction
{
    public function execute(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        if (! $session->isLocked()) {
            throw InvalidStatusTransitionException::from(
                $session->status,
                AttendanceSessionStatus::Submitted,
            );
        }

        $session->status = AttendanceSessionStatus::Submitted;
        $session->locked_at = null;
        $session->save();

        AttendanceUnlocked::dispatch($session->refresh(), $actor);

        return $session;
    }
}
