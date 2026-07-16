<?php

namespace App\Domains\Classroom\Enums;

enum RoomType: string
{
    case StandardClassroom = 'standard_classroom';
    case ScienceLaboratory = 'science_laboratory';
    case ComputerLaboratory = 'computer_laboratory';
    case Library = 'library';
    case ExaminationHall = 'examination_hall';
    case ConferenceRoom = 'conference_room';
    case MultipurposeRoom = 'multipurpose_room';
    case Workshop = 'workshop';
    case Auditorium = 'auditorium';

    public function label(): string
    {
        return match ($this) {
            self::StandardClassroom => 'Standard Classroom',
            self::ScienceLaboratory => 'Science Laboratory',
            self::ComputerLaboratory => 'Computer Laboratory',
            self::Library => 'Library',
            self::ExaminationHall => 'Examination Hall',
            self::ConferenceRoom => 'Conference Room',
            self::MultipurposeRoom => 'Multipurpose Room',
            self::Workshop => 'Workshop',
            self::Auditorium => 'Auditorium',
        };
    }
}
