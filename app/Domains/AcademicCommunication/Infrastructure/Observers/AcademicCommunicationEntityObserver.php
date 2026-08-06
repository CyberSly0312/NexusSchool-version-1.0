<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every AcademicCommunication-domain model
 * (SPR-012). Mirrors the SPR-007/SPR-008/SPR-009 observer convention.
 */
class AcademicCommunicationEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
