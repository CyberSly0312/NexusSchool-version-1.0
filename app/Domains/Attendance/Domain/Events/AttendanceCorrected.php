<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Events;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceCorrected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AttendanceRecord $record,
        public readonly AttendanceStatus $previousStatus,
        public readonly AttendanceStatus $newStatus,
        public readonly int|string $correctionId,
        public readonly string $reasonType,
        public readonly ?User $actor = null,
    ) {
    }
}
