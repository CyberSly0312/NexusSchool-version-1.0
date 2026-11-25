<?php

declare(strict_types=1);

namespace App\Domains\AccountRecovery\Enums;

enum PasswordResetFailure: string
{
    case Actor = 'actor';
    case School = 'school';
    case Target = 'target';
    case Token = 'token';
    case TokenExpired = 'token_expired';
    case TokenAlreadyUsed = 'token_already_used';
    case TokenRevoked = 'token_revoked';
    case Validation = 'validation';
    case Persistence = 'persistence';

    public function message(): string
    {
        return match ($this) {
            self::Actor => 'Seul un Super Admin actif peut demander cette réinitialisation.',
            self::School => 'Cet établissement n’est pas disponible pour cette opération.',
            self::Target => 'L’administrateur d’établissement cible n’est pas disponible pour cette opération.',
            self::Token => 'Ce lien de réinitialisation est invalide.',
            self::TokenExpired => 'Ce lien de réinitialisation a expiré.',
            self::TokenAlreadyUsed => 'Ce lien de réinitialisation a déjà été utilisé.',
            self::TokenRevoked => 'Ce lien de réinitialisation a été révoqué.',
            self::Validation => 'Le nouveau mot de passe n’est pas valide.',
            self::Persistence => 'La réinitialisation n’a pas pu être effectuée. Aucune modification n’a été conservée.',
        };
    }
}
