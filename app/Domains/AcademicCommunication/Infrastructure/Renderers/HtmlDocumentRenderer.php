<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Renderers;

use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use Illuminate\Support\Facades\View;

/**
 * HTML renderer of the Academic Communication Engine (SPR-012).
 *
 * Renders the document through the shared `academic-communication::document`
 * view. When a visibility policy and scope are supplied, sensitive sections
 * (student, results, gradebook, attendance, decisions, guardians, summary,
 * policies) are redacted — self-scoped recipients (parents, students) only ever
 * receive the sections their policy allows.
 */
final class HtmlDocumentRenderer implements DocumentRendererContract
{
    /**
     * Content sections that are governed by the visibility policy.
     */
    private const SENSITIVE_SECTIONS = [
        'student',
        'results',
        'gradebook',
        'attendance',
        'decisions',
        'guardians',
        'summary',
        'policies',
    ];

    public function renderHtml(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string {
        $document = $version->document;

        $content = $version->content ?? [];

        $content = $this->redact($content, $visibility, $scope);

        return View::make('academic-communication.document', [
            'document' => $document,
            'version' => $version,
            'template' => $template,
            'content' => $content,
            'signatures' => $template->signatureAreas(),
            'qrCodeEnabled' => $template->qr_code_enabled,
            'verificationCodeEnabled' => $template->verification_code_enabled,
        ])->render();
    }

    public function renderPdf(
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
        DocumentChannel $channel,
        ?VisibilityPolicy $visibility = null,
        ?VisibilityScope $scope = null,
    ): string {
        $html = $this->renderHtml($version, $template, $visibility, $scope);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', false);
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * @param  array<string, mixed>  $content
     *
     * @return array<string, mixed>
     */
    private function redact(
        array $content,
        ?VisibilityPolicy $visibility,
        ?VisibilityScope $scope,
    ): array {
        if ($visibility === null || $scope === null) {
            return $content;
        }

        foreach (self::SENSITIVE_SECTIONS as $section) {
            if (! array_key_exists($section, $content)) {
                continue;
            }

            if (! $visibility->canSeeSection($scope, $section)) {
                unset($content[$section]);
            }
        }

        return $content;
    }
}
