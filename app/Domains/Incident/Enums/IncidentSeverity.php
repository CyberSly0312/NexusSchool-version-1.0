<?php

declare(strict_types=1);

namespace App\Domains\Incident\Enums;

enum IncidentSeverity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Mineur',
            self::Medium => 'Modéré',
            self::High => 'Grave',
            self::Critical => 'Critique',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'success',
            self::Medium => 'warning',
            self::High => 'danger',
            self::Critical => 'danger',
        };
    }
}
