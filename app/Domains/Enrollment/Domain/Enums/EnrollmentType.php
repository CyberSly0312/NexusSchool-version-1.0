<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Enums;

/**
 * Why an enrollment row was created. The type is set once at creation and is
 * immutable for the life of the row.
 */
enum EnrollmentType: string
{
    case New = 'new';
    case ReEnrollment = 're_enrollment';
    case Promotion = 'promotion';
    case Repetition = 'repetition';
    case TransferIn = 'transfer_in';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nouvelle inscription',
            self::ReEnrollment => 'Réinscription',
            self::Promotion => 'Passage en classe supérieure',
            self::Repetition => 'Redoublement',
            self::TransferIn => 'Transfert entrant',
        };
    }
}
