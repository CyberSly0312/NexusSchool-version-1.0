<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * A published Report Card snapshot of one term of the student's retained
 * history (SPR-015, sourced from SPR-011).
 */
final readonly class ReportCardSnapshotReference
{
    /**
     * @param  array<string, mixed>  $content
     */
    public function __construct(
        public int|string $entryId,
        public array $content,
        public int|string|null $academicDocumentId = null,
        public ?string $academicDocumentNumber = null,
        public int|string|null $yearId = null,
        public int|string|null $termId = null,
    ) {
    }
}
