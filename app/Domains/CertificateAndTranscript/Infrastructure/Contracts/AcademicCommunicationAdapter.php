<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\References\AcademicDocumentReference;
use App\Models\User;

/**
 * AcademicCommunicationContract implementation (SPR-015, ADR-021 Decision 1).
 *
 * The only seam between the Certificate & Transcript and Academic Communication
 * engines: the official completion document (numbering, template, policy, PDF,
 * publication and lifecycle registration) is owned by SPR-012. The completion
 * engine supplies the composed snapshot and never sees an AcademicDocument
 * model. Documents are tagged with the additive Transcript / Certificate
 * origins.
 */
final class AcademicCommunicationAdapter implements AcademicCommunicationContract
{
    public function __construct(
        private readonly AcademicCommunicationService $service,
    ) {
    }

    public function generateTranscriptDocument(
        string $schoolId,
        int|string $studentId,
        string $title,
        array $content,
        ?User $actor = null,
    ): AcademicDocumentReference {
        return $this->generate(
            $schoolId,
            $studentId,
            DocumentType::Transcript,
            DocumentOrigin::Transcript,
            $title,
            $content,
            $actor,
        );
    }

    public function generateCertificateDocument(
        string $schoolId,
        int|string $studentId,
        string $title,
        array $content,
        ?User $actor = null,
    ): AcademicDocumentReference {
        return $this->generate(
            $schoolId,
            $studentId,
            DocumentType::Certificate,
            DocumentOrigin::Certificate,
            $title,
            $content,
            $actor,
        );
    }

    public function publishCompletionDocument(
        string $schoolId,
        int|string $academicDocumentId,
        ?User $actor = null,
    ): void {
        $this->service->publish(
            PublishDocumentData::fromArray([
                'school_id' => $schoolId,
                'document_id' => $academicDocumentId,
            ]),
            $actor,
        );
    }

    private function generate(
        string $schoolId,
        int|string $studentId,
        DocumentType $type,
        DocumentOrigin $origin,
        string $title,
        array $content,
        ?User $actor,
    ): AcademicDocumentReference {
        $document = $this->service->generateDocument(
            GenerateDocumentData::fromArray([
                'school_id' => $schoolId,
                'document_type' => $type->value,
                'title' => $title,
                'student_id' => $studentId,
                'origin' => $origin->value,
                'context' => $content,
            ]),
            $actor,
        );

        $status = $document->getAttribute('status');
        $status = $status instanceof DocumentStatus ? $status->value : ($status === null ? null : (string) $status);

        return new AcademicDocumentReference(
            id: $document->getKey(),
            documentNumber: (string) $document->document_number,
            status: $status,
        );
    }
}
