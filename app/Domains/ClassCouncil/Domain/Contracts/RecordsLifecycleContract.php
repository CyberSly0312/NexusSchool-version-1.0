<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Contracts;

use App\Models\User;

/**
 * Lifecycle metadata gateway (SPR-014).
 *
 * The Class Council engine registers a provenance / lifecycle envelope around
 * council sessions through this contract. The implementation delegates to the
 * RecordsLifecycle engine; the Class Council domain never touches a
 * RecordLifecycle model directly.
 */
interface RecordsLifecycleContract
{
    /**
     * Register a lifecycle envelope for a newly created council session.
     */
    public function register(
        string $schoolId,
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
        int|string $sourceId,
        ?User $actor = null,
    ): void;

    /**
     * Transition the lifecycle envelope to Archived.
     */
    public function archive(
        string $schoolId,
        int|string $sourceId,
        ?User $actor = null,
    ): void;
}
