<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Enums;

/**
 * Ownership scope of an academic policy (SPR-009.5).
 *
 * A school-scoped policy applies to the whole school. Academic-year and
 * subject scoped policies narrow the applicability to a specific period or
 * subject; they are supported by the schema and the query contract but are not
 * required by any consumer yet.
 */
enum AcademicPolicyScope: string
{
    case School = 'school';
    case AcademicYear = 'academic_year';
    case Subject = 'subject';

    public function label(): string
    {
        return match ($this) {
            self::School => 'École',
            self::AcademicYear => 'Année académique',
            self::Subject => 'Matière',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $scope): string => $scope->label(),
            self::cases(),
        );
    }
}
