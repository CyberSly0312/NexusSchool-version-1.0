<?php

declare(strict_types=1);

namespace App\Domains\Shared\Enums;

enum ActiveArchivedStatus: string
{
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Archived => 'danger',
        };
    }
}
