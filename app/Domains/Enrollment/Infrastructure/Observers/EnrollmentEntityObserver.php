<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Enrollment-domain model. Mirrors
 * the SPR-002 AcademicEntityObserver / SPR-003 StudentEntityObserver convention.
 */
class EnrollmentEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
