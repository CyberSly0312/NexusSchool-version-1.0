<?php

declare(strict_types=1);

namespace App\Domains\Incident\Enums;

enum IncidentStatus: string
{
    case Open = 'open';
    case Investigating = 'investigating';
    case Resolved = 'resolved';
    case Escalated = 'escalated';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Ouvert',
            self::Investigating => 'En cours d\'enquête',
            self::Resolved => 'Résolu',
            self::Escalated => 'Escaladé',
            self::Closed => 'Fermé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'warning',
            self::Investigating => 'info',
            self::Resolved => 'success',
            self::Escalated => 'danger',
            self::Closed => 'gray',
        };
    }
}
