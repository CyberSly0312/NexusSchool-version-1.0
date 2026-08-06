<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Assessment-domain model. Mirrors
 * the SPR-007/SPR-008 observer convention.
 */
class AssessmentEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
