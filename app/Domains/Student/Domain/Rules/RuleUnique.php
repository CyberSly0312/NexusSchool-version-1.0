<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Rules;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

/**
 * School-scoped uniqueness rules for student identifiers.
 */
final class RuleUnique
{
    /**
     * @param  int|string|null  $ignoreId
     */
    public static function studentNumber(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('students', 'student_number', $schoolId, $ignoreId);
    }

    /**
     * @param  int|string|null  $ignoreId
     */
    public static function email(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('students', 'email', $schoolId, $ignoreId);
    }

    /**
     * @param  int|string|null  $ignoreId
     */
    public static function admissionNumber(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('students', 'admission_number', $schoolId, $ignoreId);
    }

    /**
     * @param  int|string|null  $ignoreId
     */
    private static function column(string $table, string $column, string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return Rule::unique($table, $column)
            ->where(fn ($query) => $query->where('school_id', $schoolId))
            ->ignore($ignoreId);
    }
}
