<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Services;

use App\Domains\Staff\Infrastructure\Models\Staff;

/**
 * Generates a sequential, school-scoped staff number in the format
 * "STF-{SCHOOL_PREFIX}-{SEQUENCE}". Because staff are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */
final class StaffNumberGenerator
{
    public const PREFIX = 'STF';

    public function generate(string $schoolId): string
    {
        $sequence = (int) Staff::query()
            ->forSchool($schoolId)
            ->where('staff_number', 'like', self::PREFIX.'-%')
            ->count();

        return $this->format($schoolId, $sequence + 1);
    }

    public function format(string $schoolId, int $sequence): string
    {
        $prefix = strtoupper(substr(md5($schoolId), 0, 6));

        return sprintf('%s-%s-%04d', self::PREFIX, $prefix, $sequence);
    }
}
