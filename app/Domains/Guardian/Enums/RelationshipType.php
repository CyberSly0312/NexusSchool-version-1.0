<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Enums;

enum RelationshipType: string
{
    case Father = 'father';
    case Mother = 'mother';
    case LegalGuardian = 'legal_guardian';
    case Grandparent = 'grandparent';
    case Uncle = 'uncle';
    case Aunt = 'aunt';
    case Brother = 'brother';
    case Sister = 'sister';
    case FosterParent = 'foster_parent';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Father => 'Père',
            self::Mother => 'Mère',
            self::LegalGuardian => 'Tuteur légal',
            self::Grandparent => 'Grand-parent',
            self::Uncle => 'Oncle',
            self::Aunt => 'Tante',
            self::Brother => 'Frère',
            self::Sister => 'Sœur',
            self::FosterParent => 'Parent adoptif',
            self::Other => 'Autre',
        };
    }
}
