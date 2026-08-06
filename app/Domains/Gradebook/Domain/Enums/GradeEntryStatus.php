<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Enums;

/**
 * Status of a single gradebook line (one student, one subject, one term)
 * (SPR-010).
 *
 * Draft lines are produced by the calculation. Validating a line records the
 * teacher's acknowledgement of the figure; locking propagates the gradebook
 * lock. Published lines are the only ones the communication engine reads for
 * report cards.
 */
enum GradeEntryStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Locked = 'locked';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Validated => 'Validé',
            self::Locked => 'Verrouillé',
            self::Published => 'Publié',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Validated => 'info',
            self::Locked => 'warning',
            self::Published => 'success',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft || $this === self::Validated;
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
