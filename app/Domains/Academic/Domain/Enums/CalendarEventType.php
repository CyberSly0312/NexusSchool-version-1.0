<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Enums;

enum CalendarEventType: string
{
    case Holiday = 'holiday';
    case Exam = 'exam';
    case Meeting = 'meeting';
    case Deadline = 'deadline';
    case Event = 'event';

    public function label(): string
    {
        return match ($this) {
            self::Holiday => 'Vacances',
            self::Exam => 'Examen',
            self::Meeting => 'Réunion',
            self::Deadline => 'Échéance',
            self::Event => 'Événement',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Holiday => 'info',
            self::Exam => 'danger',
            self::Meeting => 'warning',
            self::Deadline => 'gray',
            self::Event => 'success',
        };
    }
}
