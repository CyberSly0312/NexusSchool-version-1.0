<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

use App\Domains\Shared\Enums\ActiveArchivedStatus;

/**
 * Lifecycle status of a Guardian aggregate. Only soft-delete (archived) is
 * allowed; guardians are never hard-deleted.
 */
enum GuardianStatus: string
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

    public function isImmutable(): bool
    {
        return $this === self::Archived;
    }
}
