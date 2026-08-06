<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Enums;

/**
 * Delivery state of a PublicationRecord (SPR-012).
 *
 * One PublicationRecord is created per (document, channel, recipient). The
 * engine never mutates a completed record: a re-delivery creates a new record.
 */
enum PublicationStatus: string
{
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'En file d\'attente',
            self::InProgress => 'En cours',
            self::Completed => 'Livré',
            self::Cancelled => 'Annulé',
            self::Failed => 'Échec',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Queued => 'gray',
            self::InProgress => 'info',
            self::Completed => 'success',
            self::Cancelled => 'gray',
            self::Failed => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::Completed || $this === self::Cancelled || $this === self::Failed;
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
