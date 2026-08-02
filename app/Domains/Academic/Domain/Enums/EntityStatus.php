<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Enums;

use App\Domains\Shared\Enums\DraftActiveArchivedStatus;

/**
 * Status shared by the structural catalogue entities: education levels, programs,
 * streams, classes, sections, subject groups and subjects.
 */
enum EntityStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Inactive => 'Inactif',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => DraftActiveArchivedStatus::Active->color(),
            self::Inactive => 'gray',
            self::Archived => DraftActiveArchivedStatus::Archived->color(),
        };
    }
}
