<?php

declare(strict_types=1);

namespace App\Domains\Subject\Enums;

use App\Domains\Shared\Enums\ActiveArchivedStatus;

// This enum delegates to the shared ActiveArchivedStatus enum.
// Use App\Domains\Shared\Enums\ActiveArchivedStatus in new code.
enum SubjectStatus: string
{
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return ActiveArchivedStatus::from($this->value)->label();
    }

    public function color(): string
    {
        return ActiveArchivedStatus::from($this->value)->color();
    }
}
