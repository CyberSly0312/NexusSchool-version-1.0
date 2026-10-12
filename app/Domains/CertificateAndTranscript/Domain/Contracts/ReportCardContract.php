<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\ReportCardSnapshotReference;

/**
 * Read-only gateway to the Report Card engine (SPR-015).
 *
 * The authoritative historical source for transcripts is the set of PUBLISHED
 * Report Card snapshots: the immutable report_card_entries.content rows of the
 * student's retained history, each traced to its published SPR-012 document.
 * The transcript assembles those snapshots verbatim — the engine never
 * recomputes a figure.
 */
interface ReportCardContract
{
    /**
     * Published report-card snapshots of one student's retained history.
     *
     * @return list<ReportCardSnapshotReference>
     */
    public function publishedSnapshots(string $schoolId, int|string $studentId): array;
}
