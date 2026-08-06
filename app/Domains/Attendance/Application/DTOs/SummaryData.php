<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;

/**
 * Data transfer object for materializing an attendance summary (SPR-008).
 */
final readonly class SummaryData
{
    public function __construct(
        public string $schoolId,
        public int|string $enrollmentId,
        public AttendancePeriod $period,
        public bool $dispatchAlerts = true,
    ) {
    }
}
