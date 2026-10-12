<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use Tests\Support\FakeRenderer;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeCommunicationContext($this->schoolId);

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

    $this->service = app(AcademicCommunicationService::class);
});

test('document generation registers a lifecycle envelope with correct provenance', function (): void {
    $document = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $this->context['template']->id,
        'policy_id' => $this->context['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'academic_communication')
        ->where('source_type', DocumentType::ReportCard->value)
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Registered)
        ->and($lifecycle->school_id)->toBe($this->schoolId)
        ->and($lifecycle->source_version)->toBe($document->document_number)
        ->and($lifecycle->origin_event)->toBe('DocumentGenerated');
});

test('document publication transitions the lifecycle envelope to published', function (): void {
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $this->context['template']->id,
        'policy_id' => $this->context['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'academic_communication')
        ->where('source_type', DocumentType::ReportCard->value)
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Published)
        ->and($lifecycle->published_at)->not->toBeNull();
});

test('document archival transitions the lifecycle envelope to archived', function (): void {
    $document = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $this->context['template']->id,
        'policy_id' => $this->context['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));

    $this->service->archive($document);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'academic_communication')
        ->where('source_type', DocumentType::ReportCard->value)
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('document publish-then-archive transitions lifecycle through published to archived', function (): void {
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin de test',
        'template_id' => $this->context['template']->id,
        'policy_id' => $this->context['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    $this->service->archive($document->refresh());

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'academic_communication')
        ->where('source_type', DocumentType::ReportCard->value)
        ->where('source_id', (string) $document->getKey())
        ->first();

    expect($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->published_at)->not->toBeNull()
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('lifecycle records from different schools remain isolated', function (): void {
    $otherSchool = makeTenant('Other School');
    $otherContext = makeCommunicationContext($otherSchool);

    $documentA = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin A',
        'template_id' => $this->context['template']->id,
        'policy_id' => $this->context['policy']->id,
        'context' => ['student' => ['full_name' => 'Jean Dupont']],
    ]));

    $documentB = $this->service->generateDocument(GenerateDocumentData::fromArray([
        'school_id' => $otherSchool,
        'document_type' => DocumentType::ReportCard->value,
        'title' => 'Bulletin B',
        'template_id' => $otherContext['template']->id,
        'policy_id' => $otherContext['policy']->id,
        'context' => ['student' => ['full_name' => 'Marie Curie']],
    ]));

    expect(RecordLifecycle::query()->forSchool($this->schoolId)->count())->toBe(1)
        ->and(RecordLifecycle::query()->forSchool($otherSchool)->count())->toBe(1);

    $lifecycleA = RecordLifecycle::query()->forSchool($this->schoolId)->first();
    $lifecycleB = RecordLifecycle::query()->forSchool($otherSchool)->first();

    expect($lifecycleA->school_id)->toBe($this->schoolId)
        ->and($lifecycleB->school_id)->toBe($otherSchool)
        ->and($lifecycleA->source_id)->not->toBe($lifecycleB->source_id);
});
