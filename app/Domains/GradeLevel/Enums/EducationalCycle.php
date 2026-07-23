<?php

declare(strict_types=1);

namespace App\Domains\GradeLevel\Enums;

enum EducationalCycle: string
{
    case Nursery = 'nursery';
    case Primary = 'primary';
    case JuniorSecondary = 'junior_secondary';
    case SeniorSecondary = 'senior_secondary';
    case HigherEducation = 'higher_education';
    case Vocational = 'vocational';

    public function label(): string
    {
        return match ($this) {
            self::Nursery => 'Maternelle',
            self::Primary => 'Primaire',
            self::JuniorSecondary => 'Collège',
            self::SeniorSecondary => 'Lycée',
            self::HigherEducation => 'Enseignement supérieur',
            self::Vocational => 'Professionnel',
        };
    }
}
