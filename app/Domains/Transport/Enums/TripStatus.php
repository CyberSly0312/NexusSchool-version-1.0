<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum TripStatus: string
{
    case Scheduled = 'scheduled';
    case Started = 'started';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Archived = 'archived';

    public static function labels(): array
    {
        return [
            self::Scheduled->value => 'Scheduled',
            self::Started->value => 'Started',
            self::Completed->value => 'Completed',
            self::Cancelled->value => 'Cancelled',
            self::Archived->value => 'Archived',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Scheduled->value => 'info',
            self::Started->value => 'primary',
            self::Completed->value => 'success',
            self::Cancelled->value => 'danger',
            self::Archived->value => 'gray',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Scheduled->value => 'heroicon-o-calendar',
            self::Started->value => 'heroicon-o-play',
            self::Completed->value => 'heroicon-o-check-circle',
            self::Cancelled->value => 'heroicon-o-x-circle',
            self::Archived->value => 'heroicon-o-archive-box',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Scheduled => in_array($target, [self::Started, self::Cancelled]),
            self::Started => in_array($target, [self::Completed, self::Cancelled]),
            self::Completed => in_array($target, [self::Archived]),
            self::Cancelled => in_array($target, [self::Scheduled]),
            self::Archived => in_array($target, [self::Scheduled]),
        };
    }
}
