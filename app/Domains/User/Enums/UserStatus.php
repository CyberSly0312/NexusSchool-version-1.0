<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Pending => 'En attente',
            self::Suspended => 'Suspendu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Pending => 'warning',
            self::Suspended => 'danger',
        };
    }
}
