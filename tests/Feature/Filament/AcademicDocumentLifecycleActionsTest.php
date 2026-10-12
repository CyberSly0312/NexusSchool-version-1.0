<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource\Pages\ViewAcademicDocument;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Support\FakeRenderer;
use Tests\TestCase;

/**
 * Review/approve/publish/archive lifecycle actions on the AcademicDocument
 * surface (SPR-012).
 *
 * The view page exposes the workflow to operators, delegating to the canonical
 * service and gating each action on the document capabilities (never roles).
 */
class AcademicDocumentLifecycleActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $schoolId;

    /** @var array{template: mixed, policy: mixed, channels: array<string, mixed>} */
    protected array $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = makeTenant('Academic Communication School');

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);
        $role = Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());
        $this->admin->assignRole($role);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');

        $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

        $this->context = makeCommunicationContext($this->schoolId, [
            'channels' => [DocumentChannel::InternalPortal->value],
        ]);
    }

    private function generateDocument(): AcademicDocument
    {
        makeRoleUser($this->schoolId, 'director');

        return app(AcademicCommunicationService::class)->generateDocument(
            GenerateDocumentData::fromArray([
                'school_id' => $this->schoolId,
                'document_type' => DocumentType::ReportCard->value,
                'title' => 'Bulletin de test',
                'template_id' => $this->context['template']->id,
                'policy_id' => $this->context['policy']->id,
                'context' => ['student' => ['full_name' => 'Jean Dupont']],
            ]),
        );
    }

    private function viewerWithOnly(string ...$permissions): User
    {
        $viewer = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);

        $role = Role::firstOrCreate(['name' => 'document_viewer', 'guard_name' => 'web']);
        $role->syncPermissions(
            Permission::query()->whereIn('name', $permissions)->get(),
        );
        $viewer->assignRole($role);

        return $viewer;
    }

    public function test_publish_action_publishes_a_generated_document(): void
    {
        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionVisible('publish')
            ->callAction('publish')
            ->assertHasNoActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Published)
            ->and($document->published_at)->not->toBeNull();
    }

    public function test_publish_action_is_hidden_once_the_document_is_published(): void
    {
        $document = $this->generateDocument();

        app(AcademicCommunicationService::class)->publish(
            PublishDocumentData::fromArray([
                'school_id' => $this->schoolId,
                'document_id' => $document->getKey(),
            ]),
            $this->admin,
        );

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('publish');
    }

    public function test_archive_action_archives_a_published_document(): void
    {
        $document = $this->generateDocument();

        app(AcademicCommunicationService::class)->publish(
            PublishDocumentData::fromArray([
                'school_id' => $this->schoolId,
                'document_id' => $document->getKey(),
            ]),
            $this->admin,
        );

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionVisible('archive')
            ->callAction('archive')
            ->assertHasNoActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Archived)
            ->and($document->archived_at)->not->toBeNull();
    }

    public function test_publish_action_is_hidden_without_the_publish_capability(): void
    {
        $document = $this->generateDocument();

        $this->actingAs($this->viewerWithOnly('document.view'));

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('publish')
            ->assertActionHidden('archive');
    }

    public function test_publish_action_reports_failure_for_an_unapproved_document(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->callAction('publish')
            ->assertNotified('Publication impossible');

        expect($document->refresh()->status)->toBe(DocumentStatus::Generated);
    }

    public function test_review_action_reviews_a_generated_document(): void
    {
        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionVisible('review')
            ->callAction('review')
            ->assertHasNoActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Reviewed)
            ->and($document->reviewed_at)->not->toBeNull();
    }

    public function test_review_action_is_hidden_when_the_document_is_not_reviewable(): void
    {
        $document = $this->generateDocument();

        app(AcademicCommunicationService::class)->publish(
            PublishDocumentData::fromArray([
                'school_id' => $this->schoolId,
                'document_id' => $document->getKey(),
            ]),
            $this->admin,
        );

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('review');
    }

    public function test_review_action_is_hidden_without_the_review_capability(): void
    {
        $document = $this->generateDocument();

        $this->actingAs($this->viewerWithOnly('document.view'));

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('review');
    }

    public function test_approve_action_approves_a_document_requiring_approval(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionVisible('approve')
            ->callAction('approve', ['approver_role' => 'director'])
            ->assertHasNoActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Approved)
            ->and($document->approval_status)->toBe(ApprovalStatus::Approved);

        $approval = DocumentApproval::query()->forSchool($this->schoolId)->first();

        expect($approval)->not->toBeNull()
            ->and($approval->approver_role)->toBe('director')
            ->and($approval->approver_user_id)->toBe($this->admin->getKey());
    }

    public function test_approve_action_is_hidden_when_approval_is_not_required(): void
    {
        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('approve');
    }

    public function test_approve_action_is_hidden_without_the_approve_capability(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        $this->actingAs($this->viewerWithOnly('document.view'));

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->assertActionHidden('approve');
    }

    public function test_approval_gated_publication_succeeds_after_approval(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->callAction('approve', ['approver_role' => 'director'])
            ->assertHasNoActionErrors();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->callAction('publish')
            ->assertHasNoActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Published);
    }

    public function test_approve_action_rejects_an_invalid_role_without_changing_status(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->callAction('approve', ['approver_role' => 'secretary'])
            ->assertHasActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Generated)
            ->and($document->approval_status)->toBe(ApprovalStatus::Pending);

        expect(DocumentApproval::query()->forSchool($this->schoolId)->count())->toBe(0);
    }

    public function test_approve_action_requires_an_approval_role(): void
    {
        $this->context = $this->approvalContext();

        $document = $this->generateDocument();

        Livewire::test(ViewAcademicDocument::class, ['record' => $document->getKey()])
            ->callAction('approve', [])
            ->assertHasActionErrors();

        expect($document->refresh()->status)->toBe(DocumentStatus::Generated);
    }

    /**
     * @return array{template: mixed, policy: mixed, channels: array<string, mixed>}
     */
    private function approvalContext(): array
    {
        return makeCommunicationContext($this->schoolId, [
            'channels' => [DocumentChannel::InternalPortal->value],
            'requires_approval' => true,
            'approval_roles' => ['director'],
        ]);
    }
}
