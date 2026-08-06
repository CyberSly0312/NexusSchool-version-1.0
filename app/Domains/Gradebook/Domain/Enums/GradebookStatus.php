<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Enums;

/**
 * Lifecycle of a term/subject gradebook (SPR-010).
 *
 * Draft → Calculated → Locked → Published → Archived.
 *
 * Draft gradebooks have no academic value yet. Calculating computes the term
 * average of every enrolled student from the published assessment scores.
 * Locking freezes the entries: it is the last moment a teacher may alter a
 * figure. Publishing makes the figures visible to the communication engine
 * (report cards); archiving removes the gradebook from the active flow. The
 * engine supports an explicit Unlock operation that re-opens a locked
 * gradebook for correction.
 */
enum GradebookStatus: string
{
    case Draft = 'draft';
    case Calculated = 'calculated';
    case Locked = 'locked';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Calculated => 'Calculé',
            self::Locked => 'Verrouillé',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Calculated => 'info',
            self::Locked => 'warning',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * Entries are immutable once the gradebook is locked.
     */
    public function isImmutable(): bool
    {
        return $this === self::Locked || $this === self::Published || $this === self::Archived;
    }

    /**
     * A locked gradebook is closed, but an authorized actor may re-open it.
     */
    public function isLocked(): bool
    {
        return $this === self::Locked || $this === self::Published;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Calculated => $from === self::Draft || $from === self::Locked,
            self::Locked => $from === self::Calculated,
            self::Published => $from === self::Locked,
            self::Archived => $from === self::Draft || $from === self::Calculated || $from === self::Locked || $from === self::Published,
            self::Draft => false,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): string => $status->label(),
            self::cases(),
        );
    }
}
