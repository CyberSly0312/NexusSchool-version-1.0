<?php

declare(strict_types=1);

namespace App\Domains\Student\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Graduated = 'graduated';
    case Suspended = 'suspended';
    case Withdrawn = 'withdrawn';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Graduated => 'Graduated',
            self::Suspended => 'Suspended',
            self::Withdrawn => 'Withdrawn',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'warning',
            self::Graduated => 'info',
            self::Suspended => 'danger',
            self::Withdrawn => 'gray',
            self::Archived => 'danger',
        };
    }
}
