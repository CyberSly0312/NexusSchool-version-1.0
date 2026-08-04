<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Enums;

/**
 * Kind of transfer an enrollment went through.
 */
enum TransferType: string
{
    case Internal = 'internal';
    case SchoolTransfer = 'school_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Transfert interne',
            self::SchoolTransfer => 'Transfert d\'école',
        };
    }
}
