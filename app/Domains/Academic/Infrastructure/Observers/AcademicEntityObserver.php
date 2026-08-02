<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Single observer for every engine model: generates the public ULID `uuid`.
 *
 * This replaces the legacy per-model `booted()` generators and the global
 * `Model::creating` hook for the Academic domain (SPR-001 D9, scoped to this
 * domain until the app-wide ULID sprint lands).
 */
class AcademicEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->uuid === null) {
            $model->uuid = (string) Str::ulid();
        }
    }
}
