<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum MaintenanceType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case Emergency = 'emergency';
    case Inspection = 'inspection';

    public static function labels(): array
    {
        return [
            self::Preventive->value => 'Preventive',
            self::Corrective->value => 'Corrective',
            self::Emergency->value => 'Emergency',
            self::Inspection->value => 'Inspection',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Preventive->value => 'success',
            self::Corrective->value => 'warning',
            self::Emergency->value => 'danger',
            self::Inspection->value => 'info',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Preventive->value => 'heroicon-o-shield-check',
            self::Corrective->value => 'heroicon-o-wrench',
            self::Emergency->value => 'heroicon-o-exclamation-triangle',
            self::Inspection->value => 'heroicon-o-magnifying-glass',
        ];
    }
}
