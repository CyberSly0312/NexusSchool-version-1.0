<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiProviderStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Error = 'error';
    case RateLimited = 'rate_limited';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Error => 'Erreur',
            self::RateLimited => 'Limite de débit atteinte',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Error => 'danger',
            self::RateLimited => 'warning',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }

    public static function colors(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->color(), self::cases()),
        );
    }
}
