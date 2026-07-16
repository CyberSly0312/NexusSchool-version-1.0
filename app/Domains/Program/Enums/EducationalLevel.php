<?php

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
            self::Nursery => 'Nursery',
            self::Primary => 'Primary',
            self::JuniorSecondary => 'Junior Secondary',
            self::SeniorSecondary => 'Senior Secondary',
            self::Tertiary => 'Tertiary',
            self::Vocational => 'Vocational',
        };
    }
}
