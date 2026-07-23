<?php

declare(strict_types=1);

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
            self::Primary => 'École primaire',
            self::JuniorHigh => 'Collège',
            self::SeniorHigh => 'Lycée',
            self::Secondary => 'École secondaire',
            self::Combined => 'École mixte',
            self::Technical => 'École technique',
            self::Vocational => 'Institut professionnel',
        };
    }
}
