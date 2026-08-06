<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Time-basis of an employment contract.
 */
enum EmploymentType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Internship = 'internship';
    case Temporary = 'temporary';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Temps plein',
            self::PartTime => 'Temps partiel',
            self::Internship => 'Stage',
            self::Temporary => 'Temporaire',
        };
    }
}
