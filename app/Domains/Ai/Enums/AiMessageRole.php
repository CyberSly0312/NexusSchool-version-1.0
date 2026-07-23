<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiMessageRole: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
    case Function = 'function';

    public function label(): string
    {
        return match ($this) {
            self::System => 'Système',
            self::User => 'Utilisateur',
            self::Assistant => 'Assistant',
            self::Function => 'Fonction',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::System => 'info',
            self::User => 'primary',
            self::Assistant => 'success',
            self::Function => 'warning',
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
