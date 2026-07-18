<?php

declare(strict_types=1);

namespace App\Domains\Transport\Enums;

enum MaintenanceStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function labels(): array
    {
        return [
            self::Scheduled->value => 'Scheduled',
            self::InProgress->value => 'In Progress',
            self::Completed->value => 'Completed',
            self::Cancelled->value => 'Cancelled',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Scheduled->value => 'info',
            self::InProgress->value => 'primary',
            self::Completed->value => 'success',
            self::Cancelled->value => 'danger',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Scheduled->value => 'heroicon-o-calendar',
            self::InProgress->value => 'heroicon-o-play',
            self::Completed->value => 'heroicon-o-check-circle',
            self::Cancelled->value => 'heroicon-o-x-circle',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Scheduled => in_array($target, [self::InProgress, self::Cancelled]),
            self::InProgress => in_array($target, [self::Completed, self::Cancelled]),
            self::Completed => in_array($target, [self::Scheduled]),
            self::Cancelled => in_array($target, [self::Scheduled]),
        };
    }
}
