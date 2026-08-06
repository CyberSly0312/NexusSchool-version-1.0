<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Events\DocumentVersionCreated;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentImmutableException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\Support\FakeRenderer;

/**
 * Regeneration snapshots corrected content as a new version under the same
 * permanent identity. The published version stays immutable and is never
 * overwritten; the new version becomes the current one.
 */
function regenerateContext(string $schoolId): array
{
    $template = AcademicDocumentTemplate::factory()
        ->forSchool($schoolId)
        ->ofType(DocumentType::ReportCard)
        ->create();

    $policy = PublicationPolicy::factory()
        ->forSchool($schoolId)
        ->ofType(DocumentType::ReportCard)
        ->create();

    PublicationChannel::factory()
        ->forSchool($schoolId)
        ->forChannel(DocumentChannel::InternalPortal)
        ->create();

    $role = Role::firstOrCreate(['name' => 'director', 'guard_name' => 'web']);
    $user = \App\Models\User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return compact('template', 'policy');
}

function regenerateData(string $schoolId, object $template, object $policy): GenerateDocumentData
{
    return GenerateDocumentData::fromArray([
        'school_id' => $schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $template->id,
        'policy_id' => $policy->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont'], 'results' => ['math' => 14]],
    ]);
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $context = regenerateContext($this->schoolId);
    $this->template = $context['template'];
    $this->policy = $context['policy'];

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

    $this->service = app(AcademicCommunicationService::class);
});

function publishDocument(AcademicCommunicationService $service, string $schoolId, int $documentId): void
{
    $service->publish(PublishDocumentData::fromArray([
        'school_id' => $schoolId,
        'document_id' => $documentId,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));
}

test('regenerating a published document creates a new immutable version', function (): void {
    Event::fake([DocumentVersionCreated::class]);

    $document = $this->service->generateDocument(regenerateData($this->schoolId, $this->template, $this->policy));
    publishDocument($this->service, $this->schoolId, $document->id);

    $corrected = array_merge($document->content(), ['results' => ['math' => 16]]);

    $version = $this->service->regenerate($document->refresh(), $corrected);

    expect($version->version_number)->toBe(2)
        ->and($version->is_published)->toBeTrue()
        ->and($version->published_at)->not->toBeNull()
        ->and($version->content_hash)->toBe(\App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion::hashContent($corrected));

    $document->refresh();

    expect($document->content_version)->toBe(2)
        ->and($document->status)->toBe(DocumentStatus::Published)
        ->and($document->content)->toBe($corrected);

    $versions = $document->versions()->orderBy('version_number')->get();

    expect($versions)->toHaveCount(2)
        ->and($versions[0]->version_number)->toBe(1)
        ->and($versions[0]->content)->not->toBe($corrected)
        ->and($versions[1]->version_number)->toBe(2);

    Event::assertDispatched(DocumentVersionCreated::class);
});

test('regeneration re-renders the official file and rotates is_current', function (): void {
    $document = $this->service->generateDocument(regenerateData($this->schoolId, $this->template, $this->policy));
    publishDocument($this->service, $this->schoolId, $document->id);

    $this->service->regenerate($document->refresh(), ['student' => ['full_name' => 'Jean Dupont'], 'results' => ['math' => 16]]);

    $files = GeneratedFile::query()
        ->forSchool($this->schoolId)
        ->where('academic_document_id', $document->id)
        ->orderBy('id')
        ->get();

    expect($files)->toHaveCount(2)
        ->and($files[0]->is_current)->toBeFalse()
        ->and($files[1]->is_current)->toBeTrue()
        ->and($files[1]->file_name)->toEndWith('-v2.pdf');
});

test('regeneration keeps the permanent document identity', function (): void {
    $document = $this->service->generateDocument(regenerateData($this->schoolId, $this->template, $this->policy));
    $identity = $document->document_number;
    publishDocument($this->service, $this->schoolId, $document->id);

    $this->service->regenerate($document->refresh(), ['student' => ['full_name' => 'Jean Dupont']]);

    expect($document->refresh()->document_number)->toBe($identity);
});

test('an archived document cannot be regenerated', function (): void {
    $document = $this->service->generateDocument(regenerateData($this->schoolId, $this->template, $this->policy));
    publishDocument($this->service, $this->schoolId, $document->id);
    $this->service->archive($document->refresh());

    $this->service->regenerate($document->refresh(), ['student' => ['full_name' => 'Jean Dupont']]);
})->throws(DocumentImmutableException::class);
