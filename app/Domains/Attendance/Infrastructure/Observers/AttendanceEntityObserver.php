<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Attendance-domain model. Mirrors
 * the SPR-007 observer convention.
 */
class AttendanceEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
