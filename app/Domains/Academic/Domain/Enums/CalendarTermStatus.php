<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Enums;

enum CalendarTermStatus: string
{
    case Planned = 'planned';
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planifié',
            self::Open => 'Ouvert',
            self::Closed => 'Clôturé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'warning',
            self::Open => 'success',
            self::Closed => 'gray',
        };
    }
}
