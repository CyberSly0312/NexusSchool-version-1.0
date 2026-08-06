<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;

/**
 * Rendering seam of the Academic Communication Engine (SPR-012).
 *
 * Rendering is an Infrastructure concern: business engines never render PDFs.
 * The engine renders every document through this contract — HTML for the portal
 * channels, PDF for the official immutable file and the pdf_export channel.
 *
 * When a visibility policy and scope are supplied the renderer emits only the
 * sections that scope may see. Self-scoped recipients (parents, students)
 * always receive a redacted rendering — never the full administrative document.
 */
interface DocumentRendererContract
{
    /**
     * Render a document version to HTML (used by the portal channels).
     */
    public function renderHtml(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string;

    /**
     * Render a document version to PDF bytes (used to produce the official
     * immutable file and the pdf_export channel).
     */
    public function renderPdf(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        DocumentChannel $channel,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string;
}
