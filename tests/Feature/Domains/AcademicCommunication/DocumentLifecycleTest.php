<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Enums\PublicationStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentApproved;
use App\Domains\AcademicCommunication\Domain\Events\DocumentGenerated;
use App\Domains\AcademicCommunication\Domain\Events\DocumentPublished;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentApprovalException;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentNotApprovedException;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentPublicationFailedException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationDeliveryAttempt;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use Illuminate\Support\Facades\Event;
use Tests\Support\FakeRenderer;

function generateData(string $schoolId, AcademicDocumentTemplate $template, PublicationPolicy $policy, array $context = []): GenerateDocumentData
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
    $this->schoolId = makeTenant();
    $this->context = makeCommunicationContext($this->schoolId);

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

    $this->service = app(AcademicCommunicationService::class);
});

test('generation writes a generated document with its permanent identity', function (): void {
    Event::fake([DocumentGenerated::class]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    expect($document)->toBeInstanceOf(AcademicDocument::class)
        ->and($document->school_id)->toBe($this->schoolId)
        ->and($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->approval_status)->toBe(ApprovalStatus::NotRequired)
        ->and($document->approval_required)->toBeFalse()
        ->and($document->document_type)->toBe(DocumentType::ReportCard)
        ->and($document->content_version)->toBe(1)
        ->and($document->generated_at)->not->toBeNull()
        ->and($document->uuid)->not->toBeNull()
        ->and($document->document_number)
        ->toMatch('/^NS-\d{4}-[A-Z0-9]+-RC-\d{9}$/');

    Event::assertDispatched(DocumentGenerated::class);
});

test('generation renders the official PDF file for the first version', function (): void {
    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $version = $document->currentVersion;

    expect($version)->not->toBeNull()
        ->and($version->version_number)->toBe(1)
        ->and($version->is_published)->toBeFalse()
        ->and($version->content_hash)->not->toBeEmpty();

    $file = GeneratedFile::query()->forSchool($this->schoolId)->where('academic_document_id', $document->id)->first();

    expect($file)->not->toBeNull()
        ->and($file->is_current)->toBeTrue()
        ->and($file->mime_type)->toBe('application/pdf')
        ->and($file->checksum)->not->toBeEmpty();
});

test('generation issues sequential numbers scoped per document type', function (): void {
    $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));
    $second = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $numbers = $second->documentNumber();

    expect($numbers->sequence)->toBe(2);
});

test('a generated document can be reviewed', function (): void {
    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $reviewed = $this->service->review($document);

    expect($reviewed->status)->toBe(DocumentStatus::Reviewed)
        ->and($reviewed->reviewed_at)->not->toBeNull();
});

test('a generated document cannot be reviewed twice', function (): void {
    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));
    $this->service->review($document);

    $this->service->review($document->refresh());
})->throws(\App\Domains\AcademicCommunication\Domain\Exceptions\InvalidStatusTransitionException::class);

test('a document without approval requirement publishes directly', function (): void {
    Event::fake([DocumentPublished::class]);
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $published = $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    expect($published->status)->toBe(DocumentStatus::Published)
        ->and($published->published_at)->not->toBeNull();

    $record = PublicationRecord::query()->forSchool($this->schoolId)->first();

    expect($record)->not->toBeNull()
        ->and($record->status)->toBe(PublicationStatus::Completed)
        ->and($record->channel)->toBe(DocumentChannel::InternalPortal)
        ->and($record->recipient_type)->toBe('staff');

    $attempt = PublicationDeliveryAttempt::query()->forSchool($this->schoolId)->first();

    expect($attempt)->not->toBeNull()
        ->and($attempt->status)->toBe(PublicationStatus::Completed);

    Event::assertDispatched(DocumentPublished::class);
});

test('publishing marks the active version as published', function (): void {
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    $version = $document->refresh()->currentVersion;

    expect($version->status)->toBe(DocumentStatus::Published)
        ->and($version->is_published)->toBeTrue()
        ->and($version->published_at)->not->toBeNull();
});

test('publishing fails when no delivery completes', function (): void {
    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));
})->throws(DocumentPublicationFailedException::class);

test('publishing refuses a disabled channel', function (): void {
    $this->context['channels'][DocumentChannel::InternalPortal->value]->update(['is_enabled' => false]);
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));
})->throws(\App\Domains\AcademicCommunication\Domain\Exceptions\PublicationChannelUnavailableException::class);

test('a document requiring approval stays pending until approved', function (): void {
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    expect($document->approval_status)->toBe(ApprovalStatus::Pending)
        ->and($document->approval_required)->toBeTrue();
});

test('an unapproved pending document cannot be published', function (): void {
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));
})->throws(DocumentNotApprovedException::class);

test('approval by the required role approves the document', function (): void {
    Event::fake([DocumentApproved::class]);
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    $approved = $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'approver_role' => 'director',
        'approved' => true,
    ]));

    expect($approved->status)->toBe(DocumentStatus::Approved)
        ->and($approved->approval_status)->toBe(ApprovalStatus::Approved);

    $approval = DocumentApproval::query()->forSchool($this->schoolId)->first();

    expect($approval)->not->toBeNull()
        ->and($approval->approver_role)->toBe('director')
        ->and($approval->status)->toBe(ApprovalStatus::Approved);

    Event::assertDispatched(DocumentApproved::class);
});

test('an approved document can then be published', function (): void {
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'approver_role' => 'director',
        'approved' => true,
    ]));

    $published = $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->refresh()->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    expect($published->status)->toBe(DocumentStatus::Published);
});

test('a rejection flags the document as rejected', function (): void {
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    $rejected = $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'approver_role' => 'director',
        'approved' => false,
    ]));

    expect($rejected->status)->toBe(DocumentStatus::Generated)
        ->and($rejected->approval_status)->toBe(ApprovalStatus::Rejected);
});

test('approval rejects a role outside the policy approval roles', function (): void {
    $context = makeCommunicationContext($this->schoolId, [
        'requires_approval' => true,
        'approval_roles' => ['director'],
    ]);

    $document = $this->service->generateDocument(generateData($this->schoolId, $context['template'], $context['policy']));

    $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'approver_role' => 'secretary',
        'approved' => true,
    ]));
})->throws(DocumentApprovalException::class);

test('approval is refused for documents that do not require it', function (): void {
    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $this->service->approve(ApproveDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'approver_role' => 'director',
        'approved' => true,
    ]));
})->throws(DocumentApprovalException::class);

test('a published document can be archived', function (): void {
    makeRoleUser($this->schoolId, 'director');

    $document = $this->service->generateDocument(generateData($this->schoolId, $this->context['template'], $this->context['policy']));

    $this->service->publish(PublishDocumentData::fromArray([
        'school_id' => $this->schoolId,
        'document_id' => $document->id,
        'channels' => [DocumentChannel::InternalPortal->value],
    ]));

    $archived = $this->service->archive($document->refresh());

    expect($archived->status)->toBe(DocumentStatus::Archived)
        ->and($archived->archived_at)->not->toBeNull();
});
