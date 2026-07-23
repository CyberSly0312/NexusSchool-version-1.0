<?php

declare(strict_types=1);

namespace App\Domains\Library\Enums;

enum BookLoanStatus: string
{
    case Active = 'active';
    case Returned = 'returned';
    case Overdue = 'overdue';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Returned => 'Retourné',
            self::Overdue => 'En retard',
            self::Lost => 'Perdu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Returned => 'info',
            self::Overdue => 'danger',
            self::Lost => 'danger',
        };
    }
}
