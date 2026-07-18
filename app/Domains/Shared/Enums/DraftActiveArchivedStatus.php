<?php

declare(strict_types=1);

namespace App\Domains\Shared\Enums;

enum DraftActiveArchivedStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Active => 'success',
            self::Archived => 'danger',
        };
    }
}
