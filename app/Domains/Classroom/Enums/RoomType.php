<?php

declare(strict_types=1);

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
            self::StandardClassroom => 'Salle de classe standard',
            self::ScienceLaboratory => 'Laboratoire de sciences',
            self::ComputerLaboratory => 'Laboratoire d\'informatique',
            self::Library => 'Bibliothèque',
            self::ExaminationHall => 'Salle d\'examen',
            self::ConferenceRoom => 'Salle de conférence',
            self::MultipurposeRoom => 'Salle polyvalente',
            self::Workshop => 'Atelier',
            self::Auditorium => 'Auditorium',
        };
    }
}
