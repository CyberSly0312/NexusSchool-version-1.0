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
            self::NewAdmission => 'New Admission',
            self::ReEnrollment => 'Re-enrollment',
            self::TransferIn => 'Transfer In',
            self::TransferOut => 'Transfer Out',
            self::Promotion => 'Promotion',
            self::Repeat => 'Repeat',
            self::Exchange => 'Exchange',
        };
    }
}
