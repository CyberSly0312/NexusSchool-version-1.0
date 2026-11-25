<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

enum PasswordChangeFailure: string
{
    case Actor = 'actor';
    case Target = 'target';
    case CurrentPassword = 'current_password';
    case Validation = 'validation';
    case Persistence = 'persistence';

    public function message(): string
    {
        return match ($this) {
            self::Actor => 'Seul un utilisateur connecté peut modifier son propre mot de passe.',
            self::Target => 'Le compte cible n’est pas disponible pour cette opération.',
            self::CurrentPassword => 'Le mot de passe actuel est incorrect.',
            self::Validation => 'Le nouveau mot de passe n’est pas valide.',
            self::Persistence => 'Le mot de passe n’a pas pu être modifié. Aucune modification n’a été conservée.',
        };
    }
}
