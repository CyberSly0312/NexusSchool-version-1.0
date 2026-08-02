<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Services;

use App\Domains\Student\Infrastructure\Models\Student;

/**
 * Generates a sequential, school-scoped student number in the format
 * "STU-{SCHOOL_PREFIX}-{SEQUENCE}". Because students are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */
final class StudentNumberGenerator
{
    public const PREFIX = 'STU';

    public function generate(string $schoolId): string
    {
        $sequence = (int) Student::query()
            ->forSchool($schoolId)
            ->where('student_number', 'like', self::PREFIX.'-%')
            ->count();

        return $this->format($schoolId, $sequence + 1);
    }

    public function format(string $schoolId, int $sequence): string
    {
        $prefix = strtoupper(substr(md5($schoolId), 0, 6));

        return sprintf('%s-%s-%04d', self::PREFIX, $prefix, $sequence);
    }
}
