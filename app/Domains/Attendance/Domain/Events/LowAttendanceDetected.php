<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Events;

use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowAttendanceDetected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AttendanceAlert $alert,
        public readonly int|string $enrollmentId,
        public readonly float $attendancePercentage,
        public readonly float $threshold,
        public readonly ?User $actor = null,
    ) {
    }
}
