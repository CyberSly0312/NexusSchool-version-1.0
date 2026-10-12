<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Actions;

use App\Domains\CertificateAndTranscript\Application\Services\CertificateContentComposer;
use App\Domains\CertificateAndTranscript\Application\Services\CertificateEligibilityService;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CertificateEligibilityVerdict;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateEligibilityDetermined;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateGenerated;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CertificateIneligibilityException;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CompletionRequestException;
use App\Domains\CertificateAndTranscript\Domain\References\AcademicDocumentReference;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Generates an end-of-study certificate for a draft completion request
 * (SPR-015, ADR-021 Decision 4).
 *
 * The eligibility verdict is orchestrated from the published evidence and is
 * persisted on the request for audit before any document exists. An ineligible
 * student is refused issuance — no certificate document is produced. When
 * eligible, the official document is delegated to SPR-012 (origin
 * `DocumentOrigin::Certificate`).
 */
final class GenerateCertificateAction
{
    public function __construct(
        private readonly CertificateEligibilityService $eligibility,
        private readonly CertificateContentComposer $composer,
        private readonly StudentIdentityContract $studentIdentity,
        private readonly AcademicCommunicationContract $communication,
    ) {
    }

    public function execute(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        $completionRequest->assertCanTransitionTo(CompletionRequestStatus::Generated);

        if ($completionRequest->completion_document_kind !== CompletionDocumentKind::Certificate) {
            throw new CompletionRequestException('This action only issues certificates.');
        }

        $student = $this->requireStudent($completionRequest);

        $verdict = $this->eligibility->determine($completionRequest->school_id, $completionRequest->student_id);
        $citations = $verdict['evidence_citations'];

        // The verdict and its evidence citations are committed before anything
        // else so the audit trail keeps the refusal even when no certificate is
        // produced (ADR-021 Decision 4).
        DB::transaction(function () use ($completionRequest, $verdict, $citations): void {
            $completionRequest->eligibility_verdict = CertificateEligibilityVerdict::tryFrom((string) $verdict['verdict']);
            $completionRequest->eligibility_reasons = $verdict['reasons'];
            $completionRequest->evidence_citations = $citations;
            $completionRequest->save();
        });

        CertificateEligibilityDetermined::dispatch($completionRequest, $verdict, $citations, $actor);

        if (($verdict['verdict'] ?? '') !== CertificateEligibilityVerdict::Eligible->value) {
            throw CertificateIneligibilityException::for($completionRequest->student_id, $verdict['reasons']);
        }

        return DB::transaction(function () use ($completionRequest, $verdict, $citations, $student, $actor): CompletionRequest {
            $content = $this->composer->compose(
                $completionRequest->school_id,
                $completionRequest->student_id,
                $verdict,
                $citations,
            );

            $title = str_replace(
                '{student}',
                $student->fullName,
                (string) config('nexus-certificate-and-transcript.titles.certificate'),
            );

            $document = $this->communication->generateCertificateDocument(
                $completionRequest->school_id,
                $completionRequest->student_id,
                $title,
                $content,
                $actor,
            );

            $this->persistEntry($completionRequest, $content, $document);

            $completionRequest->status = CompletionRequestStatus::Generated;
            $completionRequest->generated_at = now();
            $completionRequest->save();

            CertificateGenerated::dispatch($completionRequest, $document->documentNumber, $actor);

            return $completionRequest->refresh();
        });
    }

    private function requireStudent(CompletionRequest $completionRequest): StudentReference
    {
        $student = $this->studentIdentity->student($completionRequest->school_id, $completionRequest->student_id);

        if ($student === null) {
            throw new CompletionRequestException("Student [{$completionRequest->student_id}] not found.");
        }

        return $student;
    }

    /**
     * @param  array<string, mixed>  $content
     */
    private function persistEntry(
        CompletionRequest $completionRequest,
        array $content,
        AcademicDocumentReference $document,
    ): void {
        $completionRequest->entries()->create([
            'school_id' => $completionRequest->school_id,
            'content' => $content,
            'academic_document_id' => $document->id,
            'academic_document_number' => $document->documentNumber,
            'academic_document_type' => CompletionDocumentKind::Certificate,
            'status' => CompletionRequestEntryStatus::Issued,
        ]);
    }
}
