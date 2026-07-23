<?php

declare(strict_types=1);

namespace App\Domains\School\Enums;

enum SchoolStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Configuration en attente',
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Suspended => 'Suspendu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Suspended => 'danger',
        };
    }
}
