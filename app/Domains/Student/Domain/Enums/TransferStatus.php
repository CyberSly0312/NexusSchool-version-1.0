<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Enums;

/**
 * Admission-side transfer status of a student record. This is an independent
 * dimension from the lifecycle status: an incoming student is still "active",
 * an outgoing one keeps its lifecycle status while the transfer is recorded.
 */
enum TransferStatus: string
{
    case None = 'none';
    case Incoming = 'incoming';
    case Outgoing = 'outgoing';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Aucun',
            self::Incoming => 'Entrant',
            self::Outgoing => 'Sortant',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::None => 'gray',
            self::Incoming => 'info',
            self::Outgoing => 'warning',
        };
    }

    public function isActive(): bool
    {
        return $this !== self::None;
    }
}
