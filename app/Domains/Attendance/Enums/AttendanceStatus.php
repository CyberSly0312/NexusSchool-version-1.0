<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Excused = 'excused';
    case Partial = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Présent',
            self::Absent => 'Absent',
            self::Late => 'En retard',
            self::Excused => 'Excusé',
            self::Partial => 'Partiel',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present => 'success',
            self::Absent => 'danger',
            self::Late => 'warning',
            self::Excused => 'info',
            self::Partial => 'gray',
        };
    }
}
