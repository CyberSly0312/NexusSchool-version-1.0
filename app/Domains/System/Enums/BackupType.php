<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum BackupType: string
{
    case Manual = 'manual';
    case Scheduled = 'scheduled';
    case PreUpdate = 'pre_update';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manuel',
            self::Scheduled => 'Planifié',
            self::PreUpdate => 'Pré-mise à jour',
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
