<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

/**
 * Explicit, guardian-specific portal-access request from a trusted workflow.
 *
 * A Guardian record alone is never consent, and an embedded guardian
 * reference is never a durable identity for this request.
 */
final readonly class ParentPortalAccessRequest
{
    public function __construct(
        public int|string $guardianId,
        public bool $portalAccessRequested,
        public bool $specificGuardianConfirmed,
    ) {
    }

    public function isComplete(): bool
    {
        return $this->portalAccessRequested
            && $this->specificGuardianConfirmed
            && (string) $this->guardianId !== '';
    }
}
