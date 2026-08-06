<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Enums;

/**
 * Lifecycle of an AttendanceSession (SPR-008).
 *
 * Draft → Open → Taking Attendance → Submitted → Locked → Archived.
 * A Locked session is immutable; corrections are only possible through the
 * authorized CorrectAttendanceAction, which writes an audit entry instead of
 * overwriting history.
 */
enum AttendanceSessionStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case TakingAttendance = 'taking_attendance';
    case Submitted = 'submitted';
    case Locked = 'locked';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Open => 'Ouverte',
            self::TakingAttendance => 'Prise des présences',
            self::Submitted => 'Soumise',
            self::Locked => 'Verrouillée',
            self::Archived => 'Archivée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Open => 'primary',
            self::TakingAttendance => 'info',
            self::Submitted => 'warning',
            self::Locked => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * Whether records can still be added or modified in this state.
     */
    public function isRecordable(): bool
    {
        return $this === self::Open || $this === self::TakingAttendance;
    }

    /**
     * A locked session is immutable.
     */
    public function isImmutable(): bool
    {
        return $this === self::Locked || $this === self::Archived;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Open => $from === self::Draft,
            self::TakingAttendance => $from === self::Open,
            self::Submitted => $from === self::TakingAttendance,
            self::Locked => $from === self::Submitted || $from === self::TakingAttendance,
            self::Archived => $from === self::Locked || $from === self::Submitted,
            self::Draft => false,
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
