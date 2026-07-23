<?php

declare(strict_types=1);

namespace App\Domains\Program\Enums;

enum EducationalLevel: string
{
    case Nursery = 'nursery';
    case Primary = 'primary';
    case JuniorSecondary = 'junior_secondary';
    case SeniorSecondary = 'senior_secondary';
    case Tertiary = 'tertiary';
    case Vocational = 'vocational';

    public function label(): string
    {
        return match ($this) {
            self::Nursery => 'Maternelle',
            self::Primary => 'Primaire',
            self::JuniorSecondary => 'Collège',
            self::SeniorSecondary => 'Lycée',
            self::Tertiary => 'Supérieur',
            self::Vocational => 'Professionnel',
        };
    }
}
