<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum AssignmentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public static function labels(): array
    {
        return [
            self::Active->value => 'Actif',
            self::Inactive->value => 'Inactif',
            self::Suspended->value => 'Suspendu',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Active->value => 'success',
            self::Inactive->value => 'warning',
            self::Suspended->value => 'danger',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Active->value => 'heroicon-o-check-circle',
            self::Inactive->value => 'heroicon-o-pause',
            self::Suspended->value => 'heroicon-o-x-circle',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Active => in_array($target, [self::Inactive, self::Suspended]),
            self::Inactive => in_array($target, [self::Active]),
            self::Suspended => in_array($target, [self::Active, self::Inactive]),
        };
    }
}
