<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Broad category of a position in the staff engine.
 */
enum PositionCategory: string
{
    case Teaching = 'teaching';
    case NonTeaching = 'non_teaching';

    public function label(): string
    {
        return match ($this) {
            self::Teaching => 'Enseignant',
            self::NonTeaching => 'Non enseignant',
        };
    }
}
