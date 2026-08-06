<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * Purpose of a room assignment. Lessons are the core use-case.
 */
enum RoomAssignmentPurpose: string
{
    case Lesson = 'lesson';
    case Assessment = 'assessment';
    case Event = 'event';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Lesson => 'Cours',
            self::Assessment => 'Évaluation',
            self::Event => 'Événement',
            self::Other => 'Autre',
        };
    }
}
