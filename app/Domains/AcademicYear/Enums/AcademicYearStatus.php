<?php

declare(strict_types=1);

namespace App\Domains\AcademicYear\Enums;

use App\Domains\Shared\Enums\DraftActiveArchivedStatus;

// This enum delegates to the shared DraftActiveArchivedStatus enum.
// Use App\Domains\Shared\Enums\DraftActiveArchivedStatus in new code.
enum AcademicYearStatus: string
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
