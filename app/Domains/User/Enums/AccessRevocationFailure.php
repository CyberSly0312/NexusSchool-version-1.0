<?php

declare(strict_types=1);

namespace App\Domains\User\Enums;

enum AccessRevocationFailure: string
{
    case Target = 'target';
    case Tenant = 'tenant';
    case Persistence = 'persistence';

    public function message(): string
    {
        return match ($this) {
            self::Target => 'Le compte dont les accès doivent être révoqués est introuvable.',
            self::Tenant => 'Le compte cible n’appartient pas à l’établissement courant.',
            self::Persistence => 'Les accès n’ont pas pu être révoqués. Aucune modification n’a été conservée.',
        };
    }
}
