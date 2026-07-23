<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Bounced = 'bounced';

    public static function labels(): array
    {
        return [
            self::Pending->value => 'En attente',
            self::Sent->value => 'Envoyé',
            self::Delivered->value => 'Livré',
            self::Failed->value => 'Échoué',
            self::Bounced->value => 'Rebondi',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Pending->value => 'gray',
            self::Sent->value => 'info',
            self::Delivered->value => 'success',
            self::Failed->value => 'danger',
            self::Bounced->value => 'warning',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Pending->value => 'heroicon-o-clock',
            self::Sent->value => 'heroicon-o-paper-airplane',
            self::Delivered->value => 'heroicon-o-check-circle',
            self::Failed->value => 'heroicon-o-x-circle',
            self::Bounced->value => 'heroicon-o-arrow-uturn-left',
        ];
    }
}
