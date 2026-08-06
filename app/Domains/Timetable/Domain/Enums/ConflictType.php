<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Enums;

/**
 * Kinds of collisions/blockers the conflict engine can detect.
 */
enum ConflictType: string
{
    case TeacherConflict = 'teacher_conflict';
    case ClassConflict = 'class_conflict';
    case SectionConflict = 'section_conflict';
    case RoomConflict = 'room_conflict';
    case DuplicatePeriod = 'duplicate_period';
    case UnavailableTeacher = 'unavailable_teacher';
    case UnavailableRoom = 'unavailable_room';
    case UnavailablePeriod = 'unavailable_period';

    public function label(): string
    {
        return match ($this) {
            self::TeacherConflict => 'Conflit enseignant',
            self::ClassConflict => 'Conflit classe',
            self::SectionConflict => 'Conflit section',
            self::RoomConflict => 'Conflit salle',
            self::DuplicatePeriod => 'Période en double',
            self::UnavailableTeacher => 'Enseignant indisponible',
            self::UnavailableRoom => 'Salle indisponible',
            self::UnavailablePeriod => 'Période indisponible',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TeacherConflict, self::ClassConflict, self::SectionConflict, self::RoomConflict => 'danger',
            self::DuplicatePeriod, self::UnavailablePeriod => 'warning',
            self::UnavailableTeacher, self::UnavailableRoom => 'info',
        };
    }
}
