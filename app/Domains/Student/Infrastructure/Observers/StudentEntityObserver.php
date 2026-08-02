<?php

declare(strict_types=1);

namespace App\Domains\Student\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Student-domain model. Mirrors the
 * SPR-002 AcademicEntityObserver convention.
 */
class StudentEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
