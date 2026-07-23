<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Enums;

enum EnrollmentType: string
{
    case NewAdmission = 'new_admission';
    case ReEnrollment = 're_enrollment';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Promotion = 'promotion';
    case Repeat = 'repeat';
    case Exchange = 'exchange';

    public function label(): string
    {
        return match ($this) {
            self::NewAdmission => 'Nouvelle admission',
            self::ReEnrollment => 'Réinscription',
            self::TransferIn => 'Transfert entrant',
            self::TransferOut => 'Transfert sortant',
            self::Promotion => 'Promotion',
            self::Repeat => 'Redoublement',
            self::Exchange => 'Échange',
        };
    }
}
