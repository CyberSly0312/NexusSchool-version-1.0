<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Academic degree level of a qualification.
 */
enum QualificationLevel: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Diploma = 'diploma';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case Doctorate = 'doctorate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primaire',
            self::Secondary => 'Secondaire',
            self::Diploma => 'Diplôme',
            self::Bachelor => 'Licence',
            self::Master => 'Master',
            self::Doctorate => 'Doctorat',
            self::Other => 'Autre',
        };
    }
}
