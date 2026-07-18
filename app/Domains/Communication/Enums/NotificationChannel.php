<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum NotificationChannel: string
{
    case Email = 'email';
    case SMS = 'sms';
    case WhatsApp = 'whatsapp';
    case Push = 'push';
    case InApp = 'in_app';

    public static function labels(): array
    {
        return [
            self::Email->value => 'Email',
            self::SMS->value => 'SMS',
            self::WhatsApp->value => 'WhatsApp',
            self::Push->value => 'Push',
            self::InApp->value => 'In-App',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Email->value => 'info',
            self::SMS->value => 'success',
            self::WhatsApp->value => 'primary',
            self::Push->value => 'warning',
            self::InApp->value => 'gray',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Email->value => 'heroicon-o-envelope',
            self::SMS->value => 'heroicon-o-device-phone-mobile',
            self::WhatsApp->value => 'heroicon-o-chat-bubble-left',
            self::Push->value => 'heroicon-o-bell',
            self::InApp->value => 'heroicon-o-window',
        ];
    }
}
