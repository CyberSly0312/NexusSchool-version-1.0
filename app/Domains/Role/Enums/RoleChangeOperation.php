<?php

declare(strict_types=1);

namespace App\Domains\Role\Enums;

enum RoleChangeOperation: string
{
    case Assign = 'assign';
    case Remove = 'remove';

    public function label(): string
    {
        return match ($this) {
            self::Assign => 'attribution',
            self::Remove => 'retrait',
        };
    }
}
