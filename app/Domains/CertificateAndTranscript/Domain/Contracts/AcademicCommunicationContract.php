<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\AcademicDocumentReference;
use App\Models\User;

/**
 * Read-only gateway to the Academic Communication engine (SPR-015).
 *
 * The engine composes the completion snapshot and delegates the official
 * document (numbering, template, policy, PDF, publication) to SPR-012 through
 * this contract. It never touches an AcademicDocument model. The DocumentOrigin
 * of the generated documents is Transcript / Certificate. Documents register
 * lifecycle records through the SPR-012 generate path.
 */
interface AcademicCommunicationContract
{
    /**
     * Generate the official transcript document for one student.
     *
     * @param  array<string, mixed>  $content
     */
    public function generateTranscriptDocument(
        string $schoolId,
        int|string $studentId,
        string $title,
        array $content,
        ?User $actor = null,
    ): AcademicDocumentReference;

    /**
     * Generate the official end-of-study certificate document for one student.
     *
     * @param  array<string, mixed>  $content
     */
    public function generateCertificateDocument(
        string $schoolId,
        int|string $studentId,
        string $title,
        array $content,
        ?User $actor = null,
    ): AcademicDocumentReference;

    /**
     * Publishes the official completion document through the channels of its
     * publication policy.
     */
    public function publishCompletionDocument(
        string $schoolId,
        int|string $academicDocumentId,
        ?User $actor = null,
    ): void;
}
