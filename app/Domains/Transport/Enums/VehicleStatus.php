<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum VehicleStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case InService = 'in_service';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    public static function labels(): array
    {
        return [
            self::Available->value => 'Available',
            self::Assigned->value => 'Assigned',
            self::InService->value => 'In Service',
            self::Maintenance->value => 'Maintenance',
            self::Retired->value => 'Retired',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Available->value => 'success',
            self::Assigned->value => 'info',
            self::InService->value => 'primary',
            self::Maintenance->value => 'warning',
            self::Retired->value => 'danger',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Available->value => 'heroicon-o-check-circle',
            self::Assigned->value => 'heroicon-o-user-group',
            self::InService->value => 'heroicon-o-play',
            self::Maintenance->value => 'heroicon-o-wrench',
            self::Retired->value => 'heroicon-o-x-circle',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Available => in_array($target, [self::Assigned, self::Maintenance]),
            self::Assigned => in_array($target, [self::InService, self::Available, self::Maintenance]),
            self::InService => in_array($target, [self::Assigned, self::Available, self::Maintenance]),
            self::Maintenance => in_array($target, [self::Available, self::Retired]),
            self::Retired => false,
        };
    }
}
