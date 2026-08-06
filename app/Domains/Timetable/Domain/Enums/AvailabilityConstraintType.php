<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * The kind of resource an availability constraint applies to. The class case is
 * exposed as ClassGroup because 'class' is a reserved keyword in PHP.
 */
enum AvailabilityConstraintType: string
{
    case Teacher = 'teacher';
    case Room = 'room';
    case ClassGroup = 'class';

    public function label(): string
    {
        return match ($this) {
            self::Teacher => 'Enseignant',
            self::Room => 'Salle',
            self::ClassGroup => 'Classe',
        };
    }
}
