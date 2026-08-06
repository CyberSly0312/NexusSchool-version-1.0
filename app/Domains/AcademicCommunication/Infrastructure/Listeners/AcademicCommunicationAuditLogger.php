<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Listeners;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentApproved;
use App\Domains\AcademicCommunication\Domain\Events\DocumentArchived;
use App\Domains\AcademicCommunication\Domain\Events\DocumentGenerated;
use App\Domains\AcademicCommunication\Domain\Events\DocumentPublished;
use App\Domains\AcademicCommunication\Domain\Events\DocumentVersionCreated;
use App\Domains\AcademicCommunication\Domain\Events\PublicationCancelled;
use App\Domains\AcademicCommunication\Domain\Events\PublicationCompleted;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentAudit;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the append-only audit trail of the Academic Communication Engine
 * (SPR-012). Every lifecycle transition lands in the engine's DocumentAudit
 * journal (with document number and version) and in the global security
 * AuditLog, mirroring the SPR-009 AssessmentAuditLogger convention.
 */
final class AcademicCommunicationAuditLogger
{
    public function handleGenerated(DocumentGenerated $event): void
    {
        $this->log($event->document, 'document_generated', $event->actor, [
            'document_number' => $event->number(),
        ]);
    }

    public function handleVersionCreated(DocumentVersionCreated $event): void
    {
        $this->log($event->document, 'document_version_created', $event->actor, [
            'version_number' => $event->version->version_number,
            'from_regeneration' => $event->fromRegeneration,
            'version_id' => $event->version->getKey(),
        ], $event->version->getKey());
    }

    public function handleApproved(DocumentApproved $event): void
    {
        $rejected = $event->approval->status === ApprovalStatus::Rejected;

        $this->log($event->document, $rejected ? 'document_rejected' : 'document_approved', $event->actor, [
            'approver_role' => $event->approval->approver_role,
            'approval_id' => $event->approval->getKey(),
            'decision' => $event->approval->status->value,
        ], $event->approval->document_version_id);
    }

    public function handlePublished(DocumentPublished $event): void
    {
        $this->log($event->document, 'document_published', $event->actor, [
            'version_number' => $event->version->version_number,
        ], $event->version->getKey());
    }

    public function handleArchived(DocumentArchived $event): void
    {
        $this->log($event->document, 'document_archived', $event->actor);
    }

    public function handlePublicationCompleted(PublicationCompleted $event): void
    {
        $this->logPublication($event->record, 'publication_completed', $event->actor, [
            'channel' => $event->record->channel->value,
            'recipient_label' => $event->record->recipient_label,
        ]);
    }

    public function handlePublicationCancelled(PublicationCancelled $event): void
    {
        $this->logPublication($event->record, 'publication_cancelled', $event->actor, [
            'channel' => $event->record->channel->value,
            'reason' => $event->reason,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function log(
        AcademicDocument $document,
        string $action,
        ?User $actor = null,
        array $payload = [],
        ?int $versionId = null,
    ): void {
        $this->journal($document, $action, $actor, $payload, $versionId);

        AuditLog::create([
            'school_id' => $document->school_id,
            'auditable_type' => $document::class,
            'auditable_id' => $document->getKey(),
            'action' => $action,
            'event' => class_basename($document),
            'description' => "Academic document [{$document->document_number}] {$action}",
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function logPublication(
        PublicationRecord $record,
        string $action,
        ?User $actor = null,
        array $payload = [],
    ): void {
        if ($record->document === null) {
            return;
        }

        $this->log($record->document, $action, $actor, $payload, $record->document_version_id);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function journal(
        AcademicDocument $document,
        string $action,
        ?User $actor,
        array $payload,
        ?int $versionId,
    ): DocumentAudit {
        return DocumentAudit::create([
            'school_id' => $document->school_id,
            'academic_document_id' => $document->getKey(),
            'document_version_id' => $versionId,
            'action' => $action,
            'actor_user_id' => $actor?->getKey(),
            'metadata' => $payload,
        ]);
    }
}
