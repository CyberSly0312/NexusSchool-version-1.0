<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum DriverStatus: string
{
    case Available = 'available';
    case OnTrip = 'on_trip';
    case OnLeave = 'on_leave';
    case Inactive = 'inactive';

    public static function labels(): array
    {
        return [
            self::Available->value => 'Available',
            self::OnTrip->value => 'On Trip',
            self::OnLeave->value => 'On Leave',
            self::Inactive->value => 'Inactive',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Available->value => 'success',
            self::OnTrip->value => 'primary',
            self::OnLeave->value => 'warning',
            self::Inactive->value => 'danger',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Available->value => 'heroicon-o-check-circle',
            self::OnTrip->value => 'heroicon-o-play',
            self::OnLeave->value => 'heroicon-o-pause',
            self::Inactive->value => 'heroicon-o-x-circle',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Available => in_array($target, [self::OnTrip, self::OnLeave, self::Inactive]),
            self::OnTrip => in_array($target, [self::Available, self::OnLeave]),
            self::OnLeave => in_array($target, [self::Available, self::Inactive]),
            self::Inactive => in_array($target, [self::Available]),
        };
    }
}
