<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\Actions;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Events\AttendanceSessionOpened;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;

/**
 * Opens a generated session so attendance can be taken (SPR-008).
 */
final class OpenAttendanceSessionAction
{
    public function execute(AttendanceSession $session, ?User $actor = null): AttendanceSession
    {
        $session->assertCanTransitionTo(AttendanceSessionStatus::Open);

        $session->status = AttendanceSessionStatus::Open;
        $session->opened_at = $session->opened_at ?? now();
        $session->save();

        AttendanceSessionOpened::dispatch($session->refresh(), $actor);

        return $session;
    }
}
