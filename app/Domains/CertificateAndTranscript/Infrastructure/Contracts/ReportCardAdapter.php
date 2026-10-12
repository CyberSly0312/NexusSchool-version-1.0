<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;

/**
 * ReportCardContract implementation (SPR-015, sourced from SPR-011).
 *
 * Projects only the PUBLISHED report-card entries of the student's retained
 * history. The snapshot content is read verbatim — the engine never recomputes
 * a figure (ADR-021 Decision 2).
 */
final class ReportCardAdapter implements ReportCardContract
{
    public function publishedSnapshots(string $schoolId, int|string $studentId): array
    {
        return ReportCardEntry::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->where('status', ReportCardEntryStatus::Included->value)
            ->whereHas('reportCard', static fn ($query) => $query
                ->whereIn('status', [ReportCardStatus::Published->value, ReportCardStatus::Archived->value]))
            ->orderBy('id')
            ->get()
            ->map(fn (ReportCardEntry $entry): ReportCardSnapshotReference => new ReportCardSnapshotReference(
                entryId: $entry->getKey(),
                content: $entry->content ?? [],
                academicDocumentId: $entry->academic_document_id,
                academicDocumentNumber: $entry->academic_document_number,
                yearId: $entry->reportCard?->getAttribute('academic_calendar_year_id'),
                termId: $entry->reportCard?->getAttribute('academic_calendar_term_id'),
            ))
            ->all();
    }
}
