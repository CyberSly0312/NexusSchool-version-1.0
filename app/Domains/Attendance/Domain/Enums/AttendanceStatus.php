<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Enums;

/**
 * The ten canonical attendance statuses (SPR-008).
 *
 * The classification rules (which status counts as present / absent / neutral)
 * are configurable via config('nexus-attendance.statuses') so schools can
 * reclassify without code changes. The enum itself only carries the fixed
 * vocabulary plus the French labels used by the UI.
 */
enum AttendanceStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Excused = 'excused';
    case Medical = 'medical';
    case AuthorizedLeave = 'authorized_leave';
    case Unexcused = 'unexcused';
    case Remote = 'remote';
    case SchoolActivity = 'school_activity';
    case Holiday = 'holiday';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Présent',
            self::Late => 'En retard',
            self::Excused => 'Excusé',
            self::Medical => 'Médical',
            self::AuthorizedLeave => 'Absence autorisée',
            self::Unexcused => 'Non excusé',
            self::Remote => 'À distance',
            self::SchoolActivity => 'Activité scolaire',
            self::Holiday => 'Congé',
            self::Absent => 'Absent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present, self::Remote, self::SchoolActivity => 'success',
            self::Late => 'warning',
            self::Excused, self::Medical, self::AuthorizedLeave => 'info',
            self::Unexcused, self::Absent => 'danger',
            self::Holiday => 'gray',
        };
    }

    /**
     * Whether the current configuration counts this status as present.
     */
    public function countsAsPresent(): bool
    {
        return (bool) $this->setting('counts_as_present');
    }

    /**
     * Whether the current configuration counts this status as absent.
     */
    public function countsAsAbsent(): bool
    {
        return (bool) $this->setting('counts_as_absent');
    }

    /**
     * Whether the current configuration excludes this status from the
     * attendance-percentage denominator entirely.
     */
    public function isNeutral(): bool
    {
        return (bool) $this->setting('neutral');
    }

    private function setting(string $key): mixed
    {
        foreach ((array) config('nexus-attendance.statuses', []) as $status) {
            if (($status['key'] ?? null) === $this->value) {
                return $status[$key] ?? null;
            }
        }

        return null;
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
