<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Infrastructure\Listeners;

use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityReplaced;
use App\Domains\IdentityAndVerification\Domain\Events\VerificationIdentityRevoked;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the global security AuditLog for verification identity lifecycle
 * transitions (SPR-016 Phase 4, ADR-022 Decision 4). Every revocation and
 * replacement lands as an append-only record on the existing AuditLog
 * mechanism — no new audit surface is introduced. The recorded reason is
 * written only into this internal audit trail and the identity record; it is
 * never exposed through the public verification surface.
 */
final class IdentityAndVerificationAuditLogger
{
    public function handleRevoked(VerificationIdentityRevoked $event): void
    {
        $this->log($event->identity, 'verification_identity_revoked', $event->actor, [
            'reason' => $event->reason,
        ]);
    }

    public function handleReplaced(VerificationIdentityReplaced $event): void
    {
        $this->log($event->identity, 'verification_identity_replaced', $event->actor, [
            'reason' => $event->reason,
            'replaced_by_document_id' => $event->successorDocumentId,
            'replaced_by_document_number' => $event->successorDocumentNumber,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function log(
        VerificationIdentity $identity,
        string $action,
        ?User $actor,
        array $payload,
    ): void {
        AuditLog::create([
            'school_id' => $identity->school_id,
            'auditable_type' => $identity::class,
            'auditable_id' => $identity->getKey(),
            'action' => $action,
            'event' => class_basename($identity),
            'description' => "Verification identity [{$identity->uuid}] {$action}",
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }
}
