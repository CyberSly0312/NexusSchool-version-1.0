<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Masculin',
            self::Female => 'Féminin',
            self::Other => 'Autre',
        };
    }
}
