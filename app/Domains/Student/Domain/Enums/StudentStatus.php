<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Enums;

/**
 * Lifecycle status of a student record.
 *
 * Transitions are enforced by the domain actions:
 * active → suspended → active (reactivated)
 * active → withdrawn
 * active → graduated (terminal)
 * {any} → archived (terminal; archived records are immutable)
 */
enum StudentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Withdrawn = 'withdrawn';
    case Graduated = 'graduated';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Suspended => 'Suspendu',
            self::Withdrawn => 'Retiré',
            self::Graduated => 'Diplômé',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Suspended => 'danger',
            self::Withdrawn => 'warning',
            self::Graduated => 'info',
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

    /**
     * Whether the record is still considered an enrolled, active student.
     */
    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
