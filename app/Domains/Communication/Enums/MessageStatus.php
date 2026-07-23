<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum MessageStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
    case Archived = 'archived';

    public static function labels(): array
    {
        return [
            self::Draft->value => 'Brouillon',
            self::Sent->value => 'Envoyé',
            self::Delivered->value => 'Livré',
            self::Read->value => 'Lu',
            self::Archived->value => 'Archivé',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Draft->value => 'gray',
            self::Sent->value => 'info',
            self::Delivered->value => 'success',
            self::Read->value => 'primary',
            self::Archived->value => 'warning',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Draft->value => 'heroicon-o-pencil',
            self::Sent->value => 'heroicon-o-paper-airplane',
            self::Delivered->value => 'heroicon-o-check-circle',
            self::Read->value => 'heroicon-o-book-open',
            self::Archived->value => 'heroicon-o-archive-box',
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::Sent, self::Archived]),
            self::Sent => in_array($target, [self::Delivered, self::Archived]),
            self::Delivered => in_array($target, [self::Read, self::Archived]),
            self::Read => in_array($target, [self::Archived]),
            self::Archived => false,
        };
    }
}
