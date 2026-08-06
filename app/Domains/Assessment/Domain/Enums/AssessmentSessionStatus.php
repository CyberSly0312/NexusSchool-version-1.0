<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Enums;

/**
 * Lifecycle of an AssessmentSession (SPR-009).
 *
 * A session is one administration instance of an assessment on a date. It is
 * a supporting entity: the assessment aggregate remains the source of truth.
 */
enum AssessmentSessionStatus: string
{
    case Scheduled = 'scheduled';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Planifiée',
            self::Ongoing => 'En cours',
            self::Completed => 'Terminée',
            self::Cancelled => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Ongoing => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Scheduled || $this === self::Ongoing;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Ongoing => $from === self::Scheduled,
            self::Completed => $from === self::Scheduled || $from === self::Ongoing,
            self::Cancelled => $from === self::Scheduled || $from === self::Ongoing,
            self::Scheduled => false,
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
