<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Observers;

use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` on creating (SPR-009.5).
 *
 * Mirrors the SPR-002 academic observer: the uuid is a public identity, the
 * auto-increment id stays internal.
 */
class AcademicPolicyEntityObserver
{
    public function creating(AcademicPolicy $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
