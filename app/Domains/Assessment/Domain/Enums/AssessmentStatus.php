<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Enums;

/**
 * Lifecycle of an Assessment (SPR-009).
 *
 * Draft → Scheduled → Published → Completed → Archived.
 *
 * Draft and Scheduled assessments are mutable: their definition can still be
 * edited. Publishing freezes the definition (title, maximum score, passing
 * score, weight and components) while scores remain recordable. Completing an
 * assessment locks its scores; archiving removes it from the active flow. Both
 * Published and Completed assessments are immutable.
 */
enum AssessmentStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Scheduled => 'Planifiée',
            self::Published => 'Publiée',
            self::Completed => 'Terminée',
            self::Archived => 'Archivée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Scheduled => 'info',
            self::Published => 'primary',
            self::Completed => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * A published assessment is definition-immutable.
     */
    public function isDefinitionImmutable(): bool
    {
        return $this === self::Published || $this === self::Completed || $this === self::Archived;
    }

    /**
     * Scores may be recorded while the assessment is scheduled or published.
     */
    public function isScoreable(): bool
    {
        return $this === self::Draft || $this === self::Scheduled || $this === self::Published;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Scheduled => $from === self::Draft,
            self::Published => $from === self::Draft || $from === self::Scheduled,
            self::Completed => $from === self::Scheduled || $from === self::Published,
            self::Archived => $from === self::Draft || $from === self::Scheduled || $from === self::Published || $from === self::Completed,
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
