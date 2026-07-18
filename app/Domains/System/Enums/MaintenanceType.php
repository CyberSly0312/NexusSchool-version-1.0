<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum MaintenanceType: string
{
    case Scheduled = 'scheduled';
    case Emergency = 'emergency';
    case Rolling = 'rolling';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Emergency => 'Emergency',
            self::Rolling => 'Rolling',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Emergency => 'danger',
            self::Rolling => 'warning',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
