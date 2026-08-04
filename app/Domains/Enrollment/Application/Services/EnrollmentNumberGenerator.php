<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Services;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

/**
 * Generates a sequential, school-scoped enrollment number in the format
 * "ENR-{SCHOOL_PREFIX}-{SEQUENCE}". Because enrollments are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */
final class EnrollmentNumberGenerator
{
    public const PREFIX = 'ENR';

    public function generate(string $schoolId): string
    {
        $sequence = (int) Enrollment::query()
            ->forSchool($schoolId)
            ->where('enrollment_number', 'like', self::PREFIX.'-%')
            ->count();

        return $this->format($schoolId, $sequence + 1);
    }

    public function format(string $schoolId, int $sequence): string
    {
        $prefix = strtoupper(substr(md5($schoolId), 0, 6));

        return sprintf('%s-%s-%04d', self::PREFIX, $prefix, $sequence);
    }
}
