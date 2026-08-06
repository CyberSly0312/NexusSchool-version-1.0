<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Events;

use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceSessionOpened
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AttendanceSession $session,
        public readonly ?User $actor = null,
    ) {
    }
}
