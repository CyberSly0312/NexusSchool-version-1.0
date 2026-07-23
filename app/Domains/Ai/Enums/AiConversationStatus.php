<?php

declare(strict_types=1);

namespace App\Domains\Ai\Enums;

enum AiConversationStatus: string
{
    case Active = 'active';
    case Archived = 'archived';
    case Summarized = 'summarized';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Archived => 'Archivé',
            self::Summarized => 'Résumé',
            self::Closed => 'Fermé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Archived => 'secondary',
            self::Summarized => 'info',
            self::Closed => 'danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Active => true,
            self::Archived => in_array($target, [self::Active]),
            self::Summarized => in_array($target, [self::Archived, self::Closed]),
            self::Closed => false,
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
