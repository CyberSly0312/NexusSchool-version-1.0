<?php

declare(strict_types=1);

namespace App\Domains\Payment\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case MobileMoney = 'mobile_money';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case Online = 'online';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::MobileMoney => 'Mobile Money',
            self::BankTransfer => 'Virement bancaire',
            self::Check => 'Chèque',
            self::Online => 'En ligne',
        };
    }
}
