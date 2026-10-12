<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle\Application\Services;

use App\Domains\RecordsLifecycle\Application\Actions\ArchiveRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\Actions\PublishRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\Actions\RegisterRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\DTOs\RecordLifecycleData;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;

final class RecordLifecycleService
{
    public function __construct(
        private readonly RegisterRecordLifecycleAction $registerRecordLifecycle,
        private readonly PublishRecordLifecycleAction $publishRecordLifecycle,
        private readonly ArchiveRecordLifecycleAction $archiveRecordLifecycle,
    ) {
    }

    public function register(RecordLifecycleData $data, ?User $actor = null): RecordLifecycle
    {
        return $this->registerRecordLifecycle->execute($data, $actor);
    }

    public function publish(int $recordId, ?User $actor = null): RecordLifecycle
    {
        return $this->publishRecordLifecycle->execute($recordId, $actor);
    }

    public function archive(int $recordId, ?User $actor = null): RecordLifecycle
    {
        return $this->archiveRecordLifecycle->execute($recordId, $actor);
    }

    public function findBySource(
        string $schoolId,
        string $sourceDomain,
        string $sourceType,
        int|string $sourceId,
    ): ?RecordLifecycle {
        return RecordLifecycle::query()
            ->forSchool($schoolId)
            ->where('source_domain', $sourceDomain)
            ->where('source_type', $sourceType)
            ->where('source_id', (string) $sourceId)
            ->first();
    }
}
