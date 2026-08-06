<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Renderers\HtmlDocumentRenderer;

/**
 * The renderer redacts sensitive sections when a visibility policy and scope are
 * supplied. Self-scoped recipients (parents, students) only ever receive the
 * sections their policy allows — never the full administrative document.
 */
function redactionContent(): array
{
    return [
        'name' => 'École Test',
        'student' => ['full_name' => 'Jean Dupont', 'class' => '6ème A', 'section' => 'A'],
        'results' => [['subject' => 'Mathématiques', 'average' => 14.5, 'best' => 18.0, 'pass_rate' => 90.0]],
        'gradebook' => [['subject' => 'Mathématiques', 'average' => 14.5, 'coefficient' => 4, 'letter_grade' => 'B', 'rank' => 3]],
        'decisions' => [['student' => 'Jean Dupont', 'summary' => 'Passage en classe supérieure']],
    ];
}

function makeRenderedVersion(string $schoolId, array $content): AcademicDocumentVersion
{
    $template = AcademicDocumentTemplate::factory()
        ->forSchool($schoolId)
        ->ofType(DocumentType::ReportCard)
        ->create();

    $document = AcademicDocument::factory()
        ->forSchool($schoolId)
        ->create([
            'academic_document_template_id' => $template->id,
            'content' => $content,
            'status' => DocumentStatus::Generated,
        ]);

    return AcademicDocumentVersion::factory()
        ->create([
            'school_id' => $schoolId,
            'academic_document_id' => $document->id,
            'version_number' => 1,
            'status' => DocumentStatus::Generated,
            'content' => $content,
            'is_published' => false,
        ]);
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->renderer = app(HtmlDocumentRenderer::class);
});

test('a director with wildcard visibility receives the full document', function (): void {
    $version = makeRenderedVersion($this->schoolId, redactionContent());

    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Director->value => ['sections' => ['*']],
    ]);

    $html = $this->renderer->renderHtml($version, $version->document->template, $policy, VisibilityScope::Director);

    expect($html)->toContain('<h2>Élève</h2>')
        ->toContain('<h2>Résultats</h2>')
        ->toContain('<h2>Carnet de notes</h2>')
        ->toContain('<h2>Décisions du conseil</h2>');
});

test('a parent only receives the sections allowed by its policy', function (): void {
    $version = makeRenderedVersion($this->schoolId, redactionContent());

    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Parent->value => ['sections' => ['student', 'results'], 'self_only' => true],
    ]);

    $html = $this->renderer->renderHtml($version, $version->document->template, $policy, VisibilityScope::Parent);

    expect($html)->toContain('<h2>Élève</h2>')
        ->toContain('<h2>Résultats</h2>')
        ->not->toContain('<h2>Carnet de notes</h2>')
        ->not->toContain('<h2>Décisions du conseil</h2>');
});

test('a teacher sees teaching sections but not council decisions', function (): void {
    $version = makeRenderedVersion($this->schoolId, redactionContent());

    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Teacher->value => ['sections' => ['student', 'gradebook']],
    ]);

    $html = $this->renderer->renderHtml($version, $version->document->template, $policy, VisibilityScope::Teacher);

    expect($html)->toContain('<h2>Élève</h2>')
        ->toContain('<h2>Carnet de notes</h2>')
        ->not->toContain('<h2>Résultats</h2>')
        ->not->toContain('<h2>Décisions du conseil</h2>');
});

test('rendering without a policy and scope leaves every section intact', function (): void {
    $version = makeRenderedVersion($this->schoolId, redactionContent());

    $html = $this->renderer->renderHtml($version, $version->document->template);

    expect($html)->toContain('<h2>Élève</h2>')
        ->toContain('<h2>Résultats</h2>')
        ->toContain('<h2>Décisions du conseil</h2>');
});
