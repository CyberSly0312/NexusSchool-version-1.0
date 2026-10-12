<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationPolicyNotFoundException;
use App\Domains\AcademicCommunication\Domain\Exceptions\TemplateNotFoundException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\FakeRenderer;

/**
 * Full communication context (template + policy + channels) used to prove that
 * every AcademicCommunication aggregate is scoped by school_id and that the
 * application layer refuses cross-school references.
 *
 * @return array{template: AcademicDocumentTemplate, policy: PublicationPolicy, channels: array<string, PublicationChannel>}
 */
function makeIsolationContext(string $schoolId, array $policyOverrides = []): array
{
    $template = AcademicDocumentTemplate::factory()
        ->forSchool($schoolId)
        ->ofType(DocumentType::ReportCard)
        ->create();

    $policy = PublicationPolicy::factory()
        ->forSchool($schoolId)
        ->ofType(DocumentType::ReportCard)
        ->create($policyOverrides);

    $channels = [];
    foreach ([DocumentChannel::InternalPortal, DocumentChannel::PdfExport] as $channel) {
        $channels[$channel->value] = PublicationChannel::factory()
            ->forSchool($schoolId)
            ->forChannel($channel)
            ->create();
    }

    return compact('template', 'policy', 'channels');
}

function isolationData(string $schoolId, AcademicDocumentTemplate $template, PublicationPolicy $policy, array $context = []): GenerateDocumentData
{
    return GenerateDocumentData::fromArray([
        'school_id' => $schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $template->id,
        'policy_id' => $policy->id,
        'context' => array_merge(['student' => ['full_name' => 'Jean Dupont']], $context),
    ]);
}

beforeEach(function (): void {
    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());
    $this->service = app(AcademicCommunicationService::class);
});

test('templates, policies and channels are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    makeIsolationContext($schoolA);

    expect(AcademicDocumentTemplate::forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicDocumentTemplate::forSchool($schoolB)->count())->toBe(0)
        ->and(PublicationPolicy::forSchool($schoolA)->count())->toBe(1)
        ->and(PublicationPolicy::forSchool($schoolB)->count())->toBe(0)
        ->and(PublicationChannel::forSchool($schoolA)->count())->toBe(2)
        ->and(PublicationChannel::forSchool($schoolB)->count())->toBe(0);
});

test('documents, versions and generated files are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeIsolationContext($schoolA);
    $this->service->generateDocument(isolationData($schoolA, $contextA['template'], $contextA['policy']));

    expect(AcademicDocument::forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicDocument::forSchool($schoolB)->count())->toBe(0)
        ->and(AcademicDocumentVersion::forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicDocumentVersion::forSchool($schoolB)->count())->toBe(0)
        ->and(GeneratedFile::forSchool($schoolA)->count())->toBe(1)
        ->and(GeneratedFile::forSchool($schoolB)->count())->toBe(0);
});

test('generation in school A refuses a template owned by school B', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB);

    $this->service->generateDocument(isolationData($schoolA, $contextB['template'], $contextB['policy']));
})->throws(TemplateNotFoundException::class);

test('generation in school A refuses a publication policy owned by school B', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeIsolationContext($schoolA);
    $contextB = makeIsolationContext($schoolB);

    $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $contextA['template']->id,
        'policy_id' => $contextB['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));
})->throws(PublicationPolicyNotFoundException::class);

test('approval scoped to school A cannot act on a document of school B', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));

    $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_id' => $document->id,
        'approver_role' => 'director',
        'approved' => true,
    ]));
})->throws(ModelNotFoundException::class);

test('document numbering is independent per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeIsolationContext($schoolA);
    $contextB = makeIsolationContext($schoolB);

    $this->service->generateDocument(isolationData($schoolA, $contextA['template'], $contextA['policy']));

    $firstB = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));

    expect($firstB->documentNumber()->sequence)->toBe(1);
});

test('publishing scoped to school A cannot act on a document of school B', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB);

    $document = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));
})->throws(ModelNotFoundException::class);
