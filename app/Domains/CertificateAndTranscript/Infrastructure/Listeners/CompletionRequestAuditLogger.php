<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Listeners;

use App\Domains\CertificateAndTranscript\Domain\Events\CertificateEligibilityDetermined;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateGenerated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestArchived;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestCreated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestPublished;
use App\Domains\CertificateAndTranscript\Domain\Events\TranscriptGenerated;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Certificate & Transcript
 * event (SPR-015): request creation, eligibility verdict, transcript/certificate
 * issuance (through SPR-012), publishing and archiving.
 */
final class CompletionRequestAuditLogger
{
    public function handleCreated(CompletionRequestCreated $event): void
    {
        $this->log(
            $event->completionRequest,
            'completion_request_created',
            "Completion request [{$event->completionRequest->displayName()}] created",
            $event->actor,
            $this->payload($event->completionRequest),
        );
    }

    public function handleEligibilityDetermined(CertificateEligibilityDetermined $event): void
    {
        $verdict = (string) ($event->verdict['verdict'] ?? 'unknown');

        $this->log(
            $event->completionRequest,
            'certificate_eligibility_determined',
            "Certificate eligibility determined as [{$verdict}] for student [{$event->completionRequest->student_id}]",
            $event->actor,
            $this->payload($event->completionRequest, [
                'eligibility_verdict' => $verdict,
                'eligibility_reasons' => $event->verdict['reasons'] ?? [],
                'evidence_citations' => $event->evidenceCitations,
            ]),
        );
    }

    public function handleTranscriptGenerated(TranscriptGenerated $event): void
    {
        $this->log(
            $event->completionRequest,
            'transcript_generated',
            "Transcript generated for student [{$event->completionRequest->student_id}] as document [{$event->documentNumber}]",
            $event->actor,
            $this->payload($event->completionRequest, [
                'document_number' => $event->documentNumber,
            ]),
        );
    }

    public function handleCertificateGenerated(CertificateGenerated $event): void
    {
        $this->log(
            $event->completionRequest,
            'certificate_generated',
            "Certificate generated for student [{$event->completionRequest->student_id}] as document [{$event->documentNumber}]",
            $event->actor,
            $this->payload($event->completionRequest, [
                'document_number' => $event->documentNumber,
            ]),
        );
    }

    public function handlePublished(CompletionRequestPublished $event): void
    {
        $this->log(
            $event->completionRequest,
            'completion_request_published',
            "Completion request [{$event->completionRequest->displayName()}] published",
            $event->actor,
            $this->payload($event->completionRequest),
        );
    }

    public function handleArchived(CompletionRequestArchived $event): void
    {
        $this->log(
            $event->completionRequest,
            'completion_request_archived',
            "Completion request [{$event->completionRequest->displayName()}] archived",
            $event->actor,
            $this->payload($event->completionRequest),
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        CompletionRequest $completionRequest,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $completionRequest->school_id,
            'auditable_type' => $completionRequest::class,
            'auditable_id' => $completionRequest->getKey(),
            'action' => $action,
            'event' => class_basename($completionRequest),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     *
     * @return array<string, mixed>
     */
    private function payload(CompletionRequest $completionRequest, array $extra = []): array
    {
        return array_merge([
            'kind' => $completionRequest->completion_document_kind->value,
            'status' => $completionRequest->status->value,
            'student_id' => $completionRequest->student_id,
        ], $extra);
    }
}
