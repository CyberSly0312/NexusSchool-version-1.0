<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * Frequency of a recurring pattern attached to a timetable / entry / slot.
 */
enum RecurrenceFrequency: string
{
    case Weekly = 'weekly';
    case BiWeekly = 'biweekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::Weekly => 'Hebdomadaire',
            self::BiWeekly => 'Bimensuel',
            self::Monthly => 'Mensuel',
        };
    }
}
