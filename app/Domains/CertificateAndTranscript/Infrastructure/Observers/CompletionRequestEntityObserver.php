<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates the public ULID `uuid` for every Certificate & Transcript model.
 * Mirrors the SPR-007/SPR-008/SPR-009/SPR-010/SPR-011 observer convention.
 */
class CompletionRequestEntityObserver
{
    public function creating(Model $model): void
    {
        if (in_array('uuid', $model->getFillable(), true) && $model->getAttribute('uuid') === null) {
            $model->setAttribute('uuid', (string) Str::ulid());
        }
    }
}
