<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Read-only verification visibility on the AcademicDocument surface (SPR-016).
 *
 * The document resource exposes the persisted verification identity facts
 * (existence, status, lifecycle timestamps) without ever surfacing the token
 * digest and without issuing or mutating anything.
 */
class AcademicDocumentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = makeTenant('Academic Communication School');

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    private function makeDocument(): AcademicDocument
    {
        return AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->published()
            ->create();
    }

    public function test_can_render_academic_documents_list_page(): void
    {
        $this->get(AcademicDocumentResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_academic_document_view_page(): void
    {
        $document = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->published()
            ->create();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee((string) $document->document_number);
    }

    public function test_view_shows_verification_status_when_identity_exists(): void
    {
        $document = $this->makeDocument();
        VerificationIdentity::factory()->forDocument($document)->create();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('Vérification')
            ->assertSee('Active');
    }

    public function test_view_shows_empty_state_when_no_identity_exists(): void
    {
        $document = $this->makeDocument();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('Aucune');
    }

    public function test_view_reflects_a_revoked_identity(): void
    {
        $document = $this->makeDocument();
        $identity = VerificationIdentity::factory()->forDocument($document)->revoked()->create();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('Revoked')
            ->assertSee($identity->revoked_at->format('d/m/Y H:i'));
    }

    public function test_view_never_exposes_the_token_digest(): void
    {
        $document = $this->makeDocument();
        $identity = VerificationIdentity::factory()->forDocument($document)->create();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertDontSee((string) $identity->token_hash)
            ->assertDontSee('token_hash');
    }

    public function test_list_shows_the_verification_status_column(): void
    {
        $document = $this->makeDocument();
        VerificationIdentity::factory()->forDocument($document)->create();

        $this->get(AcademicDocumentResource::getUrl('index'))
            ->assertSuccessful()
            ->assertSee('Vérification')
            ->assertSee('Active');
    }

    public function test_list_eager_loads_identities_without_n_plus_one(): void
    {
        foreach (range(1, 3) as $ignored) {
            VerificationIdentity::factory()->forDocument($this->makeDocument())->create();
        }

        DB::enableQueryLog();
        $this->get(AcademicDocumentResource::getUrl('index'))->assertSuccessful();
        $identityQueries = collect(DB::getQueryLog())
            ->filter(fn (array $query): bool => str_contains((string) $query['query'], 'verification_identities'))
            ->count();
        DB::disableQueryLog();

        expect($identityQueries)->toBe(1);
    }

    public function test_verification_identity_relationship_is_scoped_to_the_document(): void
    {
        $document = $this->makeDocument();
        $own = VerificationIdentity::factory()->forDocument($document)->create();

        $otherSchool = makeTenant('Other School');
        $otherDocument = AcademicDocument::factory()->forSchool($otherSchool)->published()->create();
        $other = VerificationIdentity::factory()->forDocument($otherDocument)->create();

        expect($document->verificationIdentity?->is($own))->toBeTrue();
        expect($document->verificationIdentity?->is($other))->toBeFalse();
    }

    public function test_view_shows_the_approval_status(): void
    {
        $document = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->create([
                'status' => DocumentStatus::Generated,
                'approval_status' => ApprovalStatus::Pending,
                'approval_required' => true,
            ]);

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('Approbation')
            ->assertSee('En attente');
    }

    public function test_view_shows_the_approval_history(): void
    {
        $document = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->create([
                'status' => DocumentStatus::Generated,
                'approval_status' => ApprovalStatus::Approved,
                'approval_required' => true,
            ]);

        $version = AcademicDocumentVersion::factory()->forDocument($document)->create();

        $approver = User::factory()->create([
            'school_id' => $this->schoolId,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
        ]);

        DocumentApproval::factory()->forSchool($this->schoolId)->create([
            'academic_document_id' => $document->getKey(),
            'document_version_id' => $version->getKey(),
            'approver_role' => 'principal',
            'approver_user_id' => $approver->getKey(),
            'status' => ApprovalStatus::Approved,
            'comment' => 'Conforme au règlement',
            'decided_at' => Carbon::parse('2026-03-15 10:30:00'),
        ]);

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee("Historique d'approbation")
            ->assertSee('Principal')
            ->assertSee('Approuvée')
            ->assertSee('Ada Lovelace')
            ->assertSee('Conforme au règlement')
            ->assertSee('15/03/2026 10:30');
    }

    public function test_view_renders_without_approval_records(): void
    {
        $document = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->create();

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('Aucune décision enregistrée.');
    }

    public function test_approval_history_does_not_leak_across_schools(): void
    {
        $otherSchool = makeTenant('Other School');

        $document = AcademicDocument::factory()->forSchool($this->schoolId)->create();
        $version = AcademicDocumentVersion::factory()->forDocument($document)->create();

        $otherDocument = AcademicDocument::factory()->forSchool($otherSchool)->create();
        $otherVersion = AcademicDocumentVersion::factory()->forDocument($otherDocument)->create();

        DocumentApproval::factory()->forSchool($this->schoolId)->approved()->create([
            'academic_document_id' => $document->getKey(),
            'document_version_id' => $version->getKey(),
            'approver_role' => 'principal',
            'comment' => 'DECISION-INTERNE-A',
        ]);

        DocumentApproval::factory()->forSchool($otherSchool)->approved()->create([
            'academic_document_id' => $otherDocument->getKey(),
            'document_version_id' => $otherVersion->getKey(),
            'approver_role' => 'secretary',
            'comment' => 'DECISION-EXTERNE-B',
        ]);

        DocumentApproval::factory()->forSchool($otherSchool)->approved()->create([
            'academic_document_id' => $document->getKey(),
            'document_version_id' => $version->getKey(),
            'approver_role' => 'president',
            'comment' => 'DECISION-ROGUE-C',
        ]);

        $this->get(AcademicDocumentResource::getUrl('view', ['record' => $document->getKey()]))
            ->assertSuccessful()
            ->assertSee('DECISION-INTERNE-A')
            ->assertDontSee('DECISION-EXTERNE-B')
            ->assertDontSee('DECISION-ROGUE-C');
    }

    public function test_guest_is_redirected_from_document_pages(): void
    {
        auth()->logout();

        $this->get(AcademicDocumentResource::getUrl('index'))->assertRedirect();
    }
}
