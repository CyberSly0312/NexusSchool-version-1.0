<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Rules;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

/**
 * School-scoped uniqueness rules for guardian identifiers.
 */
final class RuleUnique
{
    /**
     * @param  int|string|null  $ignoreId
     */
    public static function guardianNumber(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('guardians', 'guardian_number', $schoolId, $ignoreId);
    }

    /**
     * @param  int|string|null  $ignoreId
     */
    public static function email(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('guardians', 'email', $schoolId, $ignoreId);
    }

    /**
     * @param  int|string|null  $ignoreId
     */
    public static function phone(string $schoolId, int|string|null $ignoreId = null): Unique
    {
        return self::column('guardians', 'phone_number', $schoolId, $ignoreId);
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
