<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Services;

use App\Domains\Guardian\Infrastructure\Models\Guardian;

/**
 * Generates a sequential, school-scoped guardian number in the format
 * "GUA-{SCHOOL_PREFIX}-{SEQUENCE}". Because guardians are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */
final class GuardianNumberGenerator
{
    public const PREFIX = 'GUA';

    public function generate(string $schoolId): string
    {
        $sequence = (int) Guardian::query()
            ->forSchool($schoolId)
            ->where('guardian_number', 'like', self::PREFIX.'-%')
            ->count();

        return $this->format($schoolId, $sequence + 1);
    }

    public function format(string $schoolId, int $sequence): string
    {
        $prefix = strtoupper(substr(md5($schoolId), 0, 6));

        return sprintf('%s-%s-%04d', self::PREFIX, $prefix, $sequence);
    }
}
