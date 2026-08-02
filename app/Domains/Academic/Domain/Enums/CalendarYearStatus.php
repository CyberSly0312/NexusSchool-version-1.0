<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Enums;

use App\Domains\Shared\Enums\DraftActiveArchivedStatus;

/**
 * Lifecycle of an academic calendar year.
 */
enum CalendarYearStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return DraftActiveArchivedStatus::from($this->value)->label();
    }

    public function color(): string
    {
        return DraftActiveArchivedStatus::from($this->value)->color();
    }
}
