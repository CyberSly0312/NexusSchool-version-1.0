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
            self::Personal => 'Personal Access Token',
            self::Client => 'Client Token',
            self::Integration => 'Integration Token',
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
