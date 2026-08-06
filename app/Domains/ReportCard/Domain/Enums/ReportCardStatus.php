<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Enums;

/**
 * Lifecycle of a report card run (SPR-011).
 *
 * A report card run covers every active student of one class (optionally one
 * section) for one period. Draft → Generated → Published → Archived.
 *
 * Draft is the empty run, before any bulletin is produced. Generating composes
 * and snapshots the content of every student from the validated upstream
 * engines and delegates the official document to the Academic Communication
 * engine. Publishing makes the bulletins available to students and guardians;
 * archiving removes the run from the active flow. The engine supports an
 * explicit Regenerate operation that re-composes a published run — the audited
 * counterpart of the Gradebook Unlock.
 */
enum ReportCardStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Generated => 'Généré',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Generated => 'info',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * Entries are immutable once the run is published.
     */
    public function isImmutable(): bool
    {
        return $this === self::Published || $this === self::Archived;
    }

    /**
     * A report card may be regenerated while generated or after publication
     * (the audited re-open operation).
     */
    public function isRegenerable(): bool
    {
        return $this === self::Generated || $this === self::Published;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Generated => $from === self::Draft || $from === self::Generated || $from === self::Published,
            self::Published => $from === self::Generated,
            self::Archived => $from === self::Generated || $from === self::Published,
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
