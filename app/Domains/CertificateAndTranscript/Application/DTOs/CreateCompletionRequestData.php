<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\DTOs;

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;

/**
 * Data required to create a completion request (SPR-015).
 *
 * A request covers one student for one completion document (transcript or
 * end-of-study certificate). Requests are forward-only: a correction is a new
 * request producing a new SPR-012 document/version.
 */
final readonly class CreateCompletionRequestData
{
    public function __construct(
        public string $schoolId,
        public int|string $studentId,
        public CompletionDocumentKind $completionDocumentKind = CompletionDocumentKind::Transcript,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            studentId: $data['student_id'],
            completionDocumentKind: CompletionDocumentKind::tryFrom((string) ($data['completion_document_kind'] ?? 'transcript')) ?? CompletionDocumentKind::Transcript,
        );
    }
}
