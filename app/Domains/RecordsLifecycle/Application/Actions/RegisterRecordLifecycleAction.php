<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Application\Actions;

use App\Domains\RecordsLifecycle\Application\DTOs\RecordLifecycleData;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

final class RegisterRecordLifecycleAction
{
    public function execute(RecordLifecycleData $data, ?User $actor = null): RecordLifecycle
    {
        if ($actor !== null) {
            Gate::forUser($actor)->authorize('create', RecordLifecycle::class);

            if (! $actor->hasRole('super_admin') && $actor->school_id !== $data->schoolId) {
                throw new AuthorizationException('A record lifecycle must be registered in the actor\'s school.');
            }
        }

        $state = $data->lifecycleState !== null
            ? RecordLifecycleState::tryFrom($data->lifecycleState)
            : RecordLifecycleState::Registered;

        $record = RecordLifecycle::create([
            'school_id' => $data->schoolId,
            'source_domain' => $data->sourceDomain,
            'source_type' => $data->sourceType,
            'source_id' => $data->sourceId,
            'source_version' => $data->sourceVersion,
            'lifecycle_state' => $state ?? RecordLifecycleState::Registered,
            'academic_context' => $data->academicContext,
            'created_by' => $data->createdBy ?? $actor?->getKey(),
            'origin_event' => $data->originEvent,
        ]);

        return $record;
    }
}
