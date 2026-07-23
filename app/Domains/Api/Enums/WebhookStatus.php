<?php

declare(strict_types=1);

namespace App\Domains\Api\Enums;

enum WebhookStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Failed = 'failed';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Failed => 'Échoué',
            self::Pending => 'En attente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Failed => 'danger',
            self::Pending => 'warning',
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
