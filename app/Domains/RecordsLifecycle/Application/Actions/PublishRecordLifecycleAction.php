<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Application\Actions;

use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class PublishRecordLifecycleAction
{
    public function execute(int $recordId, ?User $actor = null): RecordLifecycle
    {
        return DB::transaction(function () use ($recordId, $actor): RecordLifecycle {
            $record = RecordLifecycle::query()->findOrFail($recordId);

            if ($actor !== null) {
                Gate::forUser($actor)->authorize('publish', $record);
            }

            $record->assertCanTransitionTo(RecordLifecycleState::Published);

            $record->update([
                'lifecycle_state' => RecordLifecycleState::Published,
                'published_at' => now(),
            ]);

            return $record->refresh();
        });
    }
}
