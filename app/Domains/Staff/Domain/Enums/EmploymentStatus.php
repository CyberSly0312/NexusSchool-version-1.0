<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Lifecycle status of an Employment contract. Only one employment may be
 * active (or on probation) for a given staff member at a time.
 */
enum EmploymentStatus: string
{
    case Active = 'active';
    case Probation = 'probation';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Probation => 'Période d\'essai',
            self::Terminated => 'Terminé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Probation => 'warning',
            self::Terminated => 'danger',
        };
    }

    public function isTerminated(): bool
    {
        return $this === self::Terminated;
    }

    public function isCurrent(): bool
    {
        return ! $this->isTerminated();
    }
}
