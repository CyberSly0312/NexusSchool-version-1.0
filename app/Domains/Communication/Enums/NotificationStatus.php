<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public static function labels(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Queued->value => 'Queued',
            self::Sent->value => 'Sent',
            self::Delivered->value => 'Delivered',
            self::Failed->value => 'Failed',
            self::Cancelled->value => 'Cancelled',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Pending->value => 'gray',
            self::Queued->value => 'info',
            self::Sent->value => 'primary',
            self::Delivered->value => 'success',
            self::Failed->value => 'danger',
            self::Cancelled->value => 'warning',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Pending->value => 'heroicon-o-clock',
            self::Queued->value => 'heroicon-o-queue-list',
            self::Sent->value => 'heroicon-o-paper-airplane',
            self::Delivered->value => 'heroicon-o-check-circle',
            self::Failed->value => 'heroicon-o-x-circle',
            self::Cancelled->value => 'heroicon-o-no-symbol',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Queued, self::Cancelled]),
            self::Queued => in_array($target, [self::Sent, self::Cancelled, self::Failed]),
            self::Sent => in_array($target, [self::Delivered, self::Failed]),
            self::Delivered => false,
            self::Failed => in_array($target, [self::Queued]),
            self::Cancelled => false,
        };
    }
}
