<?php

declare(strict_types=1);

namespace App\Domains\School\Enums;

enum SchoolAdminLifecycleFailure: string
{
    case Actor = 'actor';
    case School = 'school';
    case Target = 'target';
    case LastActive = 'last_active';
    case Email = 'email';
    case IdentityTransfer = 'identity_transfer';
    case Provisioning = 'provisioning';
    case Persistence = 'persistence';

    public function message(): string
    {
        return match ($this) {
            self::Actor => 'Seul un Super Admin actif peut modifier le cycle de vie d’un administrateur d’établissement.',
            self::School => 'Cet établissement n’est pas disponible pour cette opération.',
            self::Target => 'L’administrateur d’établissement cible n’est pas disponible pour cette opération.',
            self::LastActive => 'Le dernier administrateur d’établissement actif ne peut pas être suspendu sans remplacement sûr.',
            self::Email => 'L’adresse e-mail du remplaçant est invalide ou déjà utilisée.',
            self::IdentityTransfer => 'Le remplaçant doit utiliser une adresse e-mail différente de celle de l’administrateur sortant.',
            self::Provisioning => 'Le remplaçant n’a pas pu être créé. Aucune modification n’a été conservée.',
            self::Persistence => 'L’opération n’a pas pu être effectuée. Aucune modification n’a été conservée.',
        };
    }
}
