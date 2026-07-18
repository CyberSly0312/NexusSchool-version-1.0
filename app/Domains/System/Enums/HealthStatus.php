<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum HealthStatus: string
{
    case Healthy = 'healthy';
    case Degraded = 'degraded';
    case Unhealthy = 'unhealthy';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Healthy => 'Healthy',
            self::Degraded => 'Degraded',
            self::Unhealthy => 'Unhealthy',
            self::Unknown => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Healthy => 'success',
            self::Degraded => 'warning',
            self::Unhealthy => 'danger',
            self::Unknown => 'gray',
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
