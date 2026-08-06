<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Policies;

use App\Domains\Attendance\Domain\Traits\HandlesAttendanceAuthorization;

final class AttendanceCorrectionPolicy
{
    use HandlesAttendanceAuthorization;

    protected function permissionGroup(): string
    {
        return 'attendance';
    }
}
