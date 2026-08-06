<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Kind of a time-aware staff assignment: department, position or campus
 * (multi-campus support). Assignments carry a validity window and never
 * overwrite the immutable employment history.
 */
enum AssignmentType: string
{
    case Department = 'department';
    case Position = 'position';
    case Campus = 'campus';

    public function label(): string
    {
        return match ($this) {
            self::Department => 'Département',
            self::Position => 'Poste',
            self::Campus => 'Campus',
        };
    }
}
