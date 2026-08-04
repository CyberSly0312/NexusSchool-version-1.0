<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Listeners;

use App\Domains\Guardian\Domain\Events\EmergencyContactAssigned;
use App\Domains\Guardian\Domain\Events\GuardianCreated;
use App\Domains\Guardian\Domain\Events\GuardianLinked;
use App\Domains\Guardian\Domain\Events\GuardianUnlinked;
use App\Domains\Guardian\Domain\Events\GuardianUpdated;
use App\Domains\Guardian\Domain\Events\PickupAuthorizationGranted;
use App\Domains\Guardian\Domain\Events\PrimaryGuardianAssigned;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) and the immutable business
 * timeline (StudentGuardianRelationshipHistory) for every Guardian-domain
 * event.
 */
final class GuardianAuditLogger
{
    public function handleGuardianCreated(GuardianCreated $event): void
    {
        $this->logGuardian($event->guardian, 'created', 'Guardian registered', $event->actor);
    }

    public function handleGuardianUpdated(GuardianUpdated $event): void
    {
        $this->logGuardian($event->guardian, 'updated', 'Guardian record updated', $event->actor);
    }

    public function handleGuardianLinked(GuardianLinked $event): void
    {
        $this->logRelationship($event->relationship, 'linked', 'Guardian linked to student', $event->actor);
    }

    public function handleGuardianUnlinked(GuardianUnlinked $event): void
    {
        $this->logRelationship(
            $event->relationship,
            'unlinked',
            $event->reason !== null ? "Guardian unlinked from student: {$event->reason}" : 'Guardian unlinked from student',
            $event->actor,
        );
    }

    public function handlePrimaryGuardianAssigned(PrimaryGuardianAssigned $event): void
    {
        $this->logRelationship($event->relationship, 'primary_assigned', 'Primary guardian assigned', $event->actor);
    }

    public function handleEmergencyContactAssigned(EmergencyContactAssigned $event): void
    {
        $action = $event->granted ? 'emergency_assigned' : 'emergency_revoked';
        $description = $event->granted
            ? 'Emergency contact assigned'
            : 'Emergency contact revoked';

        $this->logRelationship($event->relationship, $action, $description, $event->actor, [
            'granted' => $event->granted,
            'priority' => $event->priority,
        ]);
    }

    public function handlePickupAuthorizationGranted(PickupAuthorizationGranted $event): void
    {
        $action = $event->granted ? 'pickup_granted' : 'pickup_revoked';
        $description = $event->granted
            ? 'Pickup authorization granted'
            : 'Pickup authorization revoked';

        $this->logRelationship($event->relationship, $action, $description, $event->actor, [
            'granted' => $event->granted,
            'valid_from' => $event->validFrom,
            'valid_until' => $event->validUntil,
        ]);
    }

    private function logGuardian(Guardian $guardian, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $guardian->school_id,
            'auditable_type' => $guardian::class,
            'auditable_id' => $guardian->getKey(),
            'action' => $action,
            'event' => class_basename($guardian),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logRelationship(
        StudentGuardianRelationship $relationship,
        string $event,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $relationship->school_id,
            'auditable_type' => $relationship::class,
            'auditable_id' => $relationship->getKey(),
            'action' => $event,
            'event' => class_basename($relationship),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);

        StudentGuardianRelationshipHistory::create([
            'school_id' => $relationship->school_id,
            'student_guardian_id' => $relationship->getKey(),
            'student_id' => $relationship->student_id,
            'guardian_id' => $relationship->guardian_id,
            'event' => $event,
            'description' => $description,
            'payload' => $payload,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'occurred_at' => now(),
        ]);
    }
}
