<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Enums;

enum TriggerType: string
{
    case Event = 'event';
    case Time = 'time';
    case Manual = 'manual';
    case Api = 'api';
    case Scheduled = 'scheduled';

    public function label(): string
    {
        return match ($this) {
            self::Event => 'Déclencheur d\'événement',
            self::Time => 'Déclencheur horaire',
            self::Manual => 'Déclencheur manuel',
            self::Api => 'Déclencheur API',
            self::Scheduled => 'Déclencheur planifié',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Event => 'info',
            self::Time => 'warning',
            self::Manual => 'primary',
            self::Api => 'success',
            self::Scheduled => 'secondary',
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
