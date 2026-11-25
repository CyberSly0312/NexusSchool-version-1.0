<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

enum AccessRevocationTrigger: string
{
    case PasswordChange = 'password_change';
    case AdministrativeReset = 'administrative_reset';
    case SchoolAdminSuspension = 'school_admin_suspension';

    public function label(): string
    {
        return match ($this) {
            self::PasswordChange => 'Modification du mot de passe',
            self::AdministrativeReset => 'Réinitialisation administrative',
            self::SchoolAdminSuspension => 'Suspension d’un administrateur d’établissement',
        };
    }
}
