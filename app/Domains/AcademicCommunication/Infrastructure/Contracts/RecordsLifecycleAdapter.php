<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\RecordsLifecycle\Application\DTOs\RecordLifecycleData;
use App\Domains\RecordsLifecycle\Application\Services\RecordLifecycleService;
use App\Models\User;

/**
 * RecordsLifecycleContract implementation for Academic Communication (SPR-014).
 *
 * Bridges the Academic Communication engine to the RecordsLifecycle metadata
 * envelope: the adapter hardcodes source_domain to 'academic_communication' and
 * uses the document type as source_type. Registration, publication and archival
 * are delegated to RecordLifecycleService.
 */
final class RecordsLifecycleAdapter implements RecordsLifecycleContract
{
    private const SOURCE_DOMAIN = 'academic_communication';

    public function __construct(
        private readonly RecordLifecycleService $service,
    ) {
    }

    public function register(
        string $schoolId,
        string $documentType,
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
            'source_type' => $documentType,
            'source_id' => $sourceId,
            'source_version' => $sourceVersion,
            'academic_context' => $academicContext,
            'created_by' => $createdBy,
            'origin_event' => $originEvent,
        ]), $actor);
    }

    public function publish(
        string $schoolId,
        string $documentType,
        int|string $sourceId,
        ?User $actor = null,
    ): void {
        $record = $this->service->findBySource(
            $schoolId,
            self::SOURCE_DOMAIN,
            $documentType,
            $sourceId,
        );

        if ($record !== null) {
            $this->service->publish($record->id, $actor);
        }
    }

    public function archive(
        string $schoolId,
        string $documentType,
        int|string $sourceId,
        ?User $actor = null,
    ): void {
        $record = $this->service->findBySource(
            $schoolId,
            self::SOURCE_DOMAIN,
            $documentType,
            $sourceId,
        );

        if ($record !== null) {
            $this->service->archive($record->id, $actor);
        }
    }
}
