<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every ReportCard-domain model. Mirrors
 * the SPR-007/SPR-008/SPR-009/SPR-010 observer convention.
 */
class ReportCardEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
