<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Models\User;

/**
 * Lifecycle metadata gateway for Academic Communication documents (SPR-014).
 *
 * The Academic Communication engine registers a provenance / lifecycle
 * envelope around official documents through this contract. The implementation
 * delegates to the RecordsLifecycle engine; the Academic Communication domain
 * never touches a RecordLifecycle model directly.
 */
interface RecordsLifecycleContract
{
    /**
     * Register a lifecycle envelope for a newly generated document.
     */
    public function register(
        string $schoolId,
        string $documentType,
        int|string $sourceId,
        ?string $sourceVersion,
        ?array $academicContext,
        ?int $createdBy,
        ?string $originEvent,
        ?User $actor = null,
    ): void;

    /**
     * Transition the lifecycle envelope to Published.
     */
    public function publish(
        string $schoolId,
        string $documentType,
        int|string $sourceId,
        ?User $actor = null,
    ): void;

    /**
     * Transition the lifecycle envelope to Archived.
     */
    public function archive(
        string $schoolId,
        string $documentType,
        int|string $sourceId,
        ?User $actor = null,
    ): void;
}
