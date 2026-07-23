<?php

declare(strict_types=1);

namespace App\Domains\Api\Enums;

enum ApiTokenType: string
{
    case Personal = 'personal';
    case Client = 'client';
    case Integration = 'integration';

    public function label(): string
    {
        return match ($this) {
            self::Personal => 'Jeton d\'accès personnel',
            self::Client => 'Jeton client',
            self::Integration => 'Jeton d\'intégration',
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
