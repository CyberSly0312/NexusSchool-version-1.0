<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Contracts;

use App\Domains\ClassCouncil\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\RecordsLifecycle\Application\DTOs\RecordLifecycleData;
use App\Domains\RecordsLifecycle\Application\Services\RecordLifecycleService;
use App\Models\User;

/**
 * RecordsLifecycleContract implementation (SPR-014).
 *
 * Bridges the Class Council engine to the RecordsLifecycle metadata envelope:
 * the adapter hardcodes source_domain to 'class_council' and source_type to
 * 'council_session'. Registration, publication and archival are delegated to
 * RecordLifecycleService.
 */
final class RecordsLifecycleAdapter implements RecordsLifecycleContract
{
    private const SOURCE_DOMAIN = 'class_council';
    private const SOURCE_TYPE = 'council_session';

    public function __construct(
        private readonly RecordLifecycleService $service,
    ) {
    }

    public function register(
        string $schoolId,
        int|string $sourceId,
        ?string $sourceVersion,
        ?array $academicContext,
        ?int $createdBy,
        ?string $originEvent,
        ?User $actor = null,
    ): void {
        $this->service->register(RecordLifecycleData::fromArray([
            'school_id' => $schoolId,
            'source_domain' => self::SOURCE_DOMAIN,
            'source_type' => self::SOURCE_TYPE,
            'source_id' => $sourceId,
            'source_version' => $sourceVersion,
            'academic_context' => $academicContext,
            'created_by' => $createdBy,
            'origin_event' => $originEvent,
        ]), $actor);
    }

    public function publish(
        string $schoolId,
        int|string $sourceId,
        ?User $actor = null,
    ): void {
        $record = $this->service->findBySource(
            $schoolId,
            self::SOURCE_DOMAIN,
            self::SOURCE_TYPE,
            $sourceId,
        );

        if ($record !== null) {
            $this->service->publish($record->id, $actor);
        }
    }

    public function archive(
        string $schoolId,
        int|string $sourceId,
        ?User $actor = null,
    ): void {
        $record = $this->service->findBySource(
            $schoolId,
            self::SOURCE_DOMAIN,
            self::SOURCE_TYPE,
            $sourceId,
        );

        if ($record !== null) {
            $this->service->archive($record->id, $actor);
        }
    }
}
