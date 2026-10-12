<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Actions;

use App\Domains\CertificateAndTranscript\Application\Services\TranscriptContentComposer;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\TranscriptGenerated;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CompletionRequestException;
use App\Domains\CertificateAndTranscript\Domain\References\AcademicDocumentReference;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Generates a transcript for a draft completion request (SPR-015).
 *
 * Composes the immutable transcript snapshot from the published report-card
 * snapshots, delegates the official document to SPR-012 (origin
 * `DocumentOrigin::Transcript`) and transitions the request to Generated. The
 * engine never recomputes a figure.
 */
final class GenerateTranscriptAction
{
    public function __construct(
        private readonly TranscriptContentComposer $composer,
        private readonly StudentIdentityContract $studentIdentity,
        private readonly AcademicCommunicationContract $communication,
    ) {
    }

    public function execute(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        return DB::transaction(function () use ($completionRequest, $actor): CompletionRequest {
            $completionRequest->assertCanTransitionTo(CompletionRequestStatus::Generated);

            if ($completionRequest->completion_document_kind !== CompletionDocumentKind::Transcript) {
                throw new CompletionRequestException('This action only issues transcripts.');
            }

            $student = $this->requireStudent($completionRequest);
            $content = $this->composer->compose($completionRequest->school_id, $completionRequest->student_id);

            $title = str_replace(
                '{student}',
                $student->fullName,
                (string) config('nexus-certificate-and-transcript.titles.transcript'),
            );

            $document = $this->communication->generateTranscriptDocument(
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

            TranscriptGenerated::dispatch($completionRequest, $document->documentNumber, $actor);

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
            'academic_document_type' => CompletionDocumentKind::Transcript,
            'status' => CompletionRequestEntryStatus::Issued,
        ]);
    }
}
