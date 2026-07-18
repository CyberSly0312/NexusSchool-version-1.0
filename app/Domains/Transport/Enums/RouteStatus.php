<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum RouteStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';

    public static function labels(): array
    {
        return [
            self::Active->value => 'Active',
            self::Inactive->value => 'Inactive',
            self::Archived->value => 'Archived',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Active->value => 'success',
            self::Inactive->value => 'warning',
            self::Archived->value => 'danger',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Active->value => 'heroicon-o-check-circle',
            self::Inactive->value => 'heroicon-o-pause',
            self::Archived->value => 'heroicon-o-archive-box',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Active => in_array($target, [self::Inactive, self::Archived]),
            self::Inactive => in_array($target, [self::Active, self::Archived]),
            self::Archived => in_array($target, [self::Active]),
        };
    }
}
