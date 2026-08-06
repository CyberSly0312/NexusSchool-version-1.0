<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Enums;

/**
 * Lifecycle of a class council session (SPR-013).
 *
 * A council session convenes a class (optionally one section) for one period.
 * Draft is the empty session before it is held. Convening opens the
 * deliberation: decisions and deliberation records may be added while the
 * session is convened. Publishing freezes the session — published sessions are
 * immutable, exactly as the Class Council Rule requires. Archiving removes a
 * published session from the active flow while preserving its decisions.
 */
enum CouncilSessionStatus: string
{
    case Draft = 'draft';
    case Convened = 'convened';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Convened => 'Réuni',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Convened => 'info',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * Decisions are immutable once the session is published.
     */
    public function isImmutable(): bool
    {
        return $this === self::Published || $this === self::Archived;
    }

    /**
     * Decisions and deliberations may only be recorded while convened.
     */
    public function isDeliberating(): bool
    {
        return $this === self::Convened;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Convened => $from === self::Draft,
            self::Published => $from === self::Convened,
            self::Archived => $from === self::Published,
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
