<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ArchiveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\RegenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ReviewDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationPolicyNotFoundException;
use App\Domains\AcademicCommunication\Domain\Exceptions\TemplateNotFoundException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Permission;
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

/*
|--------------------------------------------------------------------------
| Lifecycle action tenant boundaries (review / regenerate / archive)
|--------------------------------------------------------------------------
|
| The command's school_id is the authoritative tenant context. The document
| is resolved inside that school, so a document owned by another school is
| never loaded and no state (status, timestamps, versions, records-lifecycle)
| is changed when the context is missing or does not match.
*/

test('a same-school actor can review a document', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));
    $actor = User::factory()->create(['school_id' => $school]);

    $reviewed = $this->service->review(ReviewDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
    ]), $actor);

    expect($reviewed->status)->toBe(DocumentStatus::Reviewed)
        ->and($reviewed->reviewed_at)->not->toBeNull();
});

test('a trusted internal review without an actor is still school-scoped', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    $reviewed = $this->service->review(ReviewDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
    ]));

    expect($reviewed->status)->toBe(DocumentStatus::Reviewed)
        ->and($reviewed->reviewed_at)->not->toBeNull();
});

test('a review command scoped to school A cannot act on a document of school B and leaves it untouched', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB);
    $document = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));
    $actor = User::factory()->create(['school_id' => $schoolA]);

    expect(fn () => $this->service->review(ReviewDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_id' => $document->id,
    ]), $actor))->toThrow(ModelNotFoundException::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->reviewed_at)->toBeNull()
        ->and($document->content_version)->toBe(1)
        ->and($document->versions()->count())->toBe(1);
});

test('a review command with no tenant context cannot act on a document', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    expect(fn () => $this->service->review(ReviewDocumentData::fromArray([
        'school_id' => '',
        'document_id' => $document->id,
    ])))->toThrow(ModelNotFoundException::class);

    expect($document->refresh()->status)->toBe(DocumentStatus::Generated)
        ->and($document->reviewed_at)->toBeNull();
});

test('a same-school actor can regenerate a document and create a new version', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));
    $actor = User::factory()->create(['school_id' => $school]);

    $version = $this->service->regenerate(RegenerateDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
        'content' => ['body' => 'correction'],
    ]), $actor);

    expect($version->version_number)->toBe(2);

    $document->refresh();

    expect($document->content_version)->toBe(2)
        ->and($document->versions()->count())->toBe(2);
});

test('a trusted internal regeneration without an actor is still school-scoped', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    $version = $this->service->regenerate(RegenerateDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
        'content' => ['body' => 'correction'],
    ]));

    expect($version->version_number)->toBe(2);
});

test('a regeneration command scoped to school A cannot act on a document of school B and leaves it untouched', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB);
    $document = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));
    $actor = User::factory()->create(['school_id' => $schoolA]);

    expect(fn () => $this->service->regenerate(RegenerateDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_id' => $document->id,
        'content' => ['body' => 'correction'],
    ]), $actor))->toThrow(ModelNotFoundException::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->content_version)->toBe(1)
        ->and($document->versions()->count())->toBe(1);
});

test('a regeneration command with no tenant context cannot act on a document', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    expect(fn () => $this->service->regenerate(RegenerateDocumentData::fromArray([
        'school_id' => '',
        'document_id' => $document->id,
        'content' => ['body' => 'correction'],
    ])))->toThrow(ModelNotFoundException::class);

    $document->refresh();

    expect($document->content_version)->toBe(1)
        ->and($document->versions()->count())->toBe(1);
});

test('a same-school actor can archive a document and register the lifecycle transition', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));
    $actor = User::factory()->create(['school_id' => $school]);
    $actor->givePermissionTo(Permission::firstOrCreate([
        'name' => 'records_lifecycle.archive',
        'guard_name' => 'web',
    ]));

    $archived = $this->service->archive(ArchiveDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
    ]), $actor);

    expect($archived->status)->toBe(DocumentStatus::Archived)
        ->and($archived->archived_at)->not->toBeNull();

    $lifecycle = RecordLifecycle::query()
        ->forSchool($school)
        ->where('source_domain', 'academic_communication')
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived);
});

test('a trusted internal archive without an actor preserves the records-lifecycle integration', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    $archived = $this->service->archive(ArchiveDocumentData::fromArray([
        'school_id' => $school,
        'document_id' => $document->id,
    ]));

    expect($archived->status)->toBe(DocumentStatus::Archived)
        ->and($archived->archived_at)->not->toBeNull();

    $lifecycle = RecordLifecycle::query()
        ->forSchool($school)
        ->where('source_domain', 'academic_communication')
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived);
});

test('an archive command scoped to school A cannot act on a document of school B and leaves it untouched', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextB = makeIsolationContext($schoolB);
    $document = $this->service->generateDocument(isolationData($schoolB, $contextB['template'], $contextB['policy']));
    $actor = User::factory()->create(['school_id' => $schoolA]);

    expect(fn () => $this->service->archive(ArchiveDocumentData::fromArray([
        'school_id' => $schoolA,
        'document_id' => $document->id,
    ]), $actor))->toThrow(ModelNotFoundException::class);

    $document->refresh();

    expect($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->archived_at)->toBeNull();

    expect(RecordLifecycle::query()
        ->where('source_id', (string) $document->getKey())
        ->where('lifecycle_state', RecordLifecycleState::Archived)
        ->count())->toBe(0);
});

test('an archive command with no tenant context cannot act on a document', function (): void {
    $school = makeTenant();
    $context = makeIsolationContext($school);
    $document = $this->service->generateDocument(isolationData($school, $context['template'], $context['policy']));

    expect(fn () => $this->service->archive(ArchiveDocumentData::fromArray([
        'school_id' => '',
        'document_id' => $document->id,
    ])))->toThrow(ModelNotFoundException::class);

    expect($document->refresh()->status)->toBe(DocumentStatus::Generated)
        ->and($document->archived_at)->toBeNull();

    expect(RecordLifecycle::query()
        ->where('source_id', (string) $document->getKey())
        ->where('lifecycle_state', RecordLifecycleState::Archived)
        ->count())->toBe(0);
});
