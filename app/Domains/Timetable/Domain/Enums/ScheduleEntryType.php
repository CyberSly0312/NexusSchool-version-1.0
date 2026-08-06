<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * Nature of a schedule entry. Lessons are the core use-case; the other types
 * exist so future engines (Assessment, Room Management) can book the same grid.
 */
enum ScheduleEntryType: string
{
    case Lesson = 'lesson';
    case Assessment = 'assessment';
    case Activity = 'activity';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Lesson => 'Cours',
            self::Assessment => 'Évaluation',
            self::Activity => 'Activité',
            self::Other => 'Autre',
        };
    }
}
