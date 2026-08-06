<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Services;

use App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;

/**
 * Issues the permanent identity of an AcademicDocument (SPR-012).
 *
 * Format: {platform}-{academicYear}-{schoolCode}-{documentTypeCode}-{sequence}.
 * The sequence is scoped per (school, academic year, document type) and derived
 * from the highest previously issued number, so a rollover to a new academic
 * year restarts the sequence. The identity is assigned once and never changes.
 */
final class DocumentNumberGenerator
{
    public function __construct(
        private readonly SchoolContract $school,
    ) {
    }

    public function next(
        string $schoolId,
        DocumentType $type,
        ?string $academicYearLabel = null,
    ): DocumentNumber {
        $schoolReference = $this->school->school($schoolId);
        $yearLabel = $academicYearLabel !== null && trim($academicYearLabel) !== ''
            ? (string) preg_replace('/[^A-Z0-9]/i', '', $academicYearLabel)
            : (string) now()->year;

        return DocumentNumber::fromParts(
            platform: (string) config('nexus-academic-communication.platform', 'NS'),
            academicYear: $yearLabel,
            schoolCode: $schoolReference?->code() ?? 'SCH0000',
            documentType: $type,
            sequence: $this->nextSequence($schoolId, $type, $yearLabel),
        );
    }

    private function nextSequence(string $schoolId, DocumentType $type, string $yearLabel): int
    {
        $prefix = strtoupper((string) config('nexus-academic-communication.platform', 'NS')).'-'.$yearLabel.'-';

        $max = (int) AcademicDocument::query()
            ->forSchool($schoolId)
            ->where('document_type', $type)
            ->where('document_number', 'like', $prefix.'%')
            ->selectRaw('COALESCE(MAX(CAST(SUBSTR(document_number, -9) AS INTEGER)), 0) AS max_sequence')
            ->value('max_sequence');

        return $max + 1;
    }
}
