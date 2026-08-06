<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Timetable-domain model. Mirrors the
 * SPR-002/003/005/006 observer convention.
 */
class TimetableEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
