<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;

/**
 * Data transfer object for computing attendance statistics over a period
 * (SPR-008).
 */
final readonly class StatisticsData
{
    public function __construct(
        public string $schoolId,
        public int|string $enrollmentId,
        public AttendancePeriod $period,
    ) {
    }
}
