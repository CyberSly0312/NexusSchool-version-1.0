<?php

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
            self::Father => 'Father',
            self::Mother => 'Mother',
            self::LegalGuardian => 'Legal Guardian',
            self::Grandparent => 'Grandparent',
            self::Uncle => 'Uncle',
            self::Aunt => 'Aunt',
            self::Brother => 'Brother',
            self::Sister => 'Sister',
            self::FosterParent => 'Foster Parent',
            self::Other => 'Other',
        };
    }
}
