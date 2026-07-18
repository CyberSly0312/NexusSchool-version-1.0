<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Enums;

enum EnrollmentStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Withdrawn => 'Withdrawn',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Suspended => 'danger',
            self::Completed => 'info',
            self::Cancelled => 'gray',
            self::Withdrawn => 'danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Active, self::Cancelled]),
            self::Active => in_array($target, [self::Suspended, self::Completed, self::Withdrawn, self::Cancelled]),
            self::Suspended => in_array($target, [self::Active, self::Withdrawn]),
            self::Completed => false,
            self::Cancelled => false,
            self::Withdrawn => false,
        };
    }
}
