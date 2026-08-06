<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Enums;

/**
 * Status of a single report card line (one student of a run) (SPR-011).
 *
 * Pending lines are created by the run. Generating composes the snapshot and
 * the official document: the line becomes Included. A line is Excluded when
 * the student is no longer actively enrolled and no snapshot can be composed.
 */
enum ReportCardEntryStatus: string
{
    case Pending = 'pending';
    case Included = 'included';
    case Excluded = 'excluded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Included => 'Inclus',
            self::Excluded => 'Exclu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Included => 'success',
            self::Excluded => 'warning',
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
