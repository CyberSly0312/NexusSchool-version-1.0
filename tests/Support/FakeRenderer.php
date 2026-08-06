<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;

/**
 * Deterministic renderer used by the AcademicCommunication feature tests. It
 * records the visibility arguments it was given so tests can assert that the
 * engine handed the right scope to the renderer at delivery time, without
 * paying the cost of a real DOM rendering.
 */
final class FakeRenderer implements DocumentRendererContract
{
    /** @var list<array{html: string, visibility: VisibilityPolicy|null, scope: VisibilityScope|null}> */
    public array $htmlCalls = [];

    public function renderHtml(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string {
        $this->htmlCalls[] = [
            'html' => '<!-- rendered -->',
            'visibility' => $visibility,
            'scope' => $scope,
        ];

        return '<!-- rendered -->';
    }

    public function renderPdf(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        DocumentChannel $channel,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string {
        return 'PDF-BYTES';
    }
}
