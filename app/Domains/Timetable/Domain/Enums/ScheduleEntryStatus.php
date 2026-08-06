<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * State of a single schedule entry.
 */
enum ScheduleEntryStatus: string
{
    case Scheduled = 'scheduled';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Planifié',
            self::Cancelled => 'Annulé',
            self::Rescheduled => 'Reporté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'success',
            self::Cancelled => 'danger',
            self::Rescheduled => 'warning',
        };
    }
}
