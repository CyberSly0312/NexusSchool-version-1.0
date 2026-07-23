<?php

declare(strict_types=1);

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
            self::Phone => 'Téléphone',
            self::SMS => 'SMS',
            self::Email => 'E-mail',
            self::WhatsApp => 'WhatsApp',
        };
    }
}
