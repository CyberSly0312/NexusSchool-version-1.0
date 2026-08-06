<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\ReportCard\Domain\References\AcademicDocumentReference;
use App\Models\User;

/**
 * AcademicCommunicationContract implementation (SPR-011).
 *
 * The only seam between the Report Card and Academic Communication engines:
 * bulletins are produced and delivered by the communication engine, which owns
 * numbering, templates, publication policies and the official PDF. The Report
 * Card engine supplies the composed snapshot and never sees an AcademicDocument
 * model.
 */
final class AcademicCommunicationAdapter implements AcademicCommunicationContract
{
    public function __construct(
        private readonly AcademicCommunicationService $service,
    ) {
    }

    public function generateReportCardDocument(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
        int|string|null $classId,
        int|string|null $sectionId,
        string $title,
        array $content,
        ?User $actor = null,
    ): AcademicDocumentReference {
        $document = $this->service->generateDocument(
            GenerateDocumentData::fromArray([
                'school_id' => $schoolId,
                'document_type' => DocumentType::ReportCard->value,
                'title' => $title,
                'year_id' => $yearId,
                'term_id' => $termId,
                'class_id' => $classId,
                'section_id' => $sectionId,
                'student_id' => $studentId,
                'origin' => DocumentOrigin::ReportCard->value,
                'context' => $content,
            ]),
            $actor,
        );

        return new AcademicDocumentReference(
            id: $document->getKey(),
            documentNumber: (string) $document->document_number,
            status: (string) $document->status?->value,
        );
    }

    public function publishReportCardDocument(
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
}
