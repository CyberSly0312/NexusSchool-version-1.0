<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Enums;

/**
 * Lifecycle status of an enrollment record (SPR-004).
 *
 * Transitions are enforced by the domain actions:
 * active <-> suspended (restore/reactivate)
 * active | suspended -> withdrawn
 * active -> graduated (terminal)
 * {any} -> archived (terminal; archived records are immutable history)
 */
enum EnrollmentStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Graduated = 'graduated';
    case Withdrawn = 'withdrawn';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Suspended => 'Suspendu',
            self::Graduated => 'Diplômé',
            self::Withdrawn => 'Retiré',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'danger',
            self::Graduated => 'info',
            self::Withdrawn => 'warning',
            self::Archived => 'gray',
        };
    }

    /**
     * A status from which no further modification is allowed.
     */
    public function isImmutable(): bool
    {
        return $this === self::Archived;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isSuspended(): bool
    {
        return $this === self::Suspended;
    }

    public function isGraduated(): bool
    {
        return $this === self::Graduated;
    }

    public function isWithdrawn(): bool
    {
        return $this === self::Withdrawn;
    }

    public function isArchived(): bool
    {
        return $this === self::Archived;
    }
}
