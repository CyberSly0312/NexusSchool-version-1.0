<?php

namespace App\Domains\Guardian\Enums;

enum PreferredContactMethod: string
{
    case Phone = 'phone';
    case SMS = 'sms';
    case Email = 'email';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Phone => 'Phone',
            self::SMS => 'SMS',
            self::Email => 'Email',
            self::WhatsApp => 'WhatsApp',
        };
    }
}
