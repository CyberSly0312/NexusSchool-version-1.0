<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\AcademicDocumentReference;
use App\Models\User;

/**
 * Read-only gateway to the Academic Communication engine (SPR-011).
 *
 * The Report Card engine renders and publishes bulletins *through* the
 * Academic Communication engine: it supplies the composed snapshot, the
 * communication engine owns the official document (numbering, template,
 * policy, PDF, delivery). This contract is the only seam between the two
 * engines — the Report Card engine never touches an AcademicDocument model.
 */
interface AcademicCommunicationContract
{
    /**
     * @param  array<string, mixed>  $content
     */
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
    ): AcademicDocumentReference;

    /**
     * Publishes the official document of one bulletin through the channels of
     * its publication policy.
     */
    public function publishReportCardDocument(
        string $schoolId,
        int|string $academicDocumentId,
        ?User $actor = null,
    ): void;
}
