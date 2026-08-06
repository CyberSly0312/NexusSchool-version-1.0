<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Enums;

/**
 * Lifecycle of a single AssessmentScore (SPR-009).
 *
 * Draft → Published. A Draft score can be updated through RecordScoreAction
 * (which then dispatches ScoreUpdated); once Published a score is immutable.
 */
enum ScoreStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Published => 'Publiée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
        };
    }

    public function isMutable(): bool
    {
        return $this === self::Draft;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Published => $from === self::Draft,
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
