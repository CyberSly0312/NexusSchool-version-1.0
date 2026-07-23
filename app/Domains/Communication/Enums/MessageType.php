<?php

declare(strict_types=1);

namespace App\Domains\Communication\Enums;

enum MessageType: string
{
    case Direct = 'direct';
    case Broadcast = 'broadcast';

    public static function labels(): array
    {
        return [
            self::Direct->value => 'Direct',
            self::Broadcast->value => 'Diffusion',
        ];
    }

    public static function colors(): array
    {
        return [
            self::Direct->value => 'info',
            self::Broadcast->value => 'primary',
        ];
    }

    public static function icons(): array
    {
        return [
            self::Direct->value => 'heroicon-o-chat-bubble-left-right',
            self::Broadcast->value => 'heroicon-o-megaphone',
        ];
    }
}
