<?php

namespace App\Domains\School\Enums;

enum SchoolType: string
{
    case Primary = 'primary';
    case JuniorHigh = 'junior_high';
    case SeniorHigh = 'senior_high';
    case Secondary = 'secondary';
    case Combined = 'combined';
    case Technical = 'technical';
    case Vocational = 'vocational';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primary School',
            self::JuniorHigh => 'Junior High School',
            self::SeniorHigh => 'Senior High School',
            self::Secondary => 'Secondary School',
            self::Combined => 'Combined School',
            self::Technical => 'Technical School',
            self::Vocational => 'Vocational Institute',
        };
    }
}
