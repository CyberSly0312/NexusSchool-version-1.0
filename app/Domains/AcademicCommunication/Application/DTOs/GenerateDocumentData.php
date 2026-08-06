<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;

/**
 * Input of the document generation actions (SPR-012).
 *
 * Carries the academic frame (year/term/class/section), the optional person
 * bound to the document (student, staff), the council session for minutes, the
 * template and publication policy to use, and the plain-text title. The
 * engine resolves the rest through its contracts.
 */
final readonly class GenerateDocumentData
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $schoolId,
        public DocumentType $documentType,
        public string $title,
        public int|string|null $yearId = null,
        public int|string|null $termId = null,
        public int|string|null $classId = null,
        public int|string|null $sectionId = null,
        public int|string|null $studentId = null,
        public int|string|null $staffId = null,
        public int|string|null $councilSessionId = null,
        public int|string|null $templateId = null,
        public int|string|null $policyId = null,
        public DocumentOrigin $origin = DocumentOrigin::Generated,
        public array $context = [],
        public int|string|null $actorId = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            documentType: DocumentType::from((string) $data['document_type']),
            title: (string) ($data['title'] ?? ''),
            yearId: $data['year_id'] ?? null,
            termId: $data['term_id'] ?? null,
            classId: $data['class_id'] ?? null,
            sectionId: $data['section_id'] ?? null,
            studentId: $data['student_id'] ?? null,
            staffId: $data['staff_id'] ?? null,
            councilSessionId: $data['council_session_id'] ?? null,
            templateId: $data['template_id'] ?? null,
            policyId: $data['policy_id'] ?? null,
            origin: DocumentOrigin::tryFrom((string) ($data['origin'] ?? 'generated')) ?? DocumentOrigin::Generated,
            context: $data['context'] ?? [],
            actorId: $data['actor_id'] ?? null,
        );
    }
}
