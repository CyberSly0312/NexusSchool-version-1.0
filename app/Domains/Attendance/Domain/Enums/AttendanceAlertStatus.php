<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Enums;

/**
 * Lifecycle of an attendance alert (SPR-008).
 *
 * Open when the engine raises it, Notified once the future Notifications domain
 * picks it up, Resolved once a human closes it. The engine itself only moves
 * alerts to open; the rest is out of sprint scope.
 */
enum AttendanceAlertStatus: string
{
    case Open = 'open';

    case Notified = 'notified';

    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Ouverte',
            self::Notified => 'Notifiée',
            self::Resolved => 'Résolue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'danger',
            self::Notified => 'warning',
            self::Resolved => 'success',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): string => $status->label(),
            self::cases(),
        );
    }
}
