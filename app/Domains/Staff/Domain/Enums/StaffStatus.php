<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Availability status of a Staff aggregate. Staff are never hard-deleted; the
 * lifecycle is managed through these states (SPR-006).
 */
enum StaffStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Retired = 'retired';
    case Resigned = 'resigned';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Suspended => 'Suspendu',
            self::Retired => 'Retraité',
            self::Resigned => 'Démissionnaire',
            self::Terminated => 'Licencié',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive, self::Retired => 'gray',
            self::Suspended => 'warning',
            self::Resigned, self::Terminated => 'danger',
        };
    }

    public function isImmutable(): bool
    {
        return in_array($this, [self::Retired, self::Resigned, self::Terminated], true);
    }
}
