<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource\Pages\ViewVerificationIdentity;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VerificationIdentityResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = makeTenant('Verification School');

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

    private function makePublishedIdentity(): VerificationIdentity
    {
        $document = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->published()
            ->create();

        return VerificationIdentity::factory()->forDocument($document)->create();
    }

    public function test_can_render_verification_identities_list_page(): void
    {
        $this->get('/school-admin/verification-identities')
            ->assertSuccessful();
    }

    public function test_can_render_verification_identity_view_page(): void
    {
        $identity = $this->makePublishedIdentity();

        $this->get('/school-admin/verification-identities/'.$identity->getKey())
            ->assertSuccessful()
            ->assertSee((string) $identity->uuid);
    }

    public function test_can_revoke_an_active_verification_identity(): void
    {
        $identity = $this->makePublishedIdentity();

        Livewire::test(ViewVerificationIdentity::class, ['record' => $identity->getKey()])
            ->callAction('revoke', data: ['reason' => 'Document émis par erreur.'])
            ->assertHasNoActionErrors();

        $identity->refresh();

        expect($identity->status)->toBe(VerificationStatus::Revoked);
        expect($identity->revoked_at)->not->toBeNull();
        expect($identity->revocation_reason)->toBe('Document émis par erreur.');
    }

    public function test_can_replace_an_active_verification_identity(): void
    {
        $identity = $this->makePublishedIdentity();

        $successor = AcademicDocument::factory()
            ->forSchool($this->schoolId)
            ->published()
            ->create();

        Livewire::test(ViewVerificationIdentity::class, ['record' => $identity->getKey()])
            ->callAction('replace', data: [
                'successor_document_id' => $successor->getKey(),
                'reason' => 'Correction du document original.',
            ])
            ->assertHasNoActionErrors();

        $identity->refresh();

        expect($identity->status)->toBe(VerificationStatus::Replaced);
        expect($identity->replaced_at)->not->toBeNull();
        expect($identity->replaced_by_document_number)->toBe($successor->document_number);
    }

    public function test_lifecycle_actions_are_hidden_for_a_terminal_identity(): void
    {
        $identity = $this->makePublishedIdentity();
        $identity->update([
            'status' => VerificationStatus::Revoked,
            'revocation_reason' => 'Déjà révoquée.',
            'revoked_at' => now(),
        ]);

        Livewire::test(ViewVerificationIdentity::class, ['record' => $identity->getKey()])
            ->assertActionHidden('revoke')
            ->assertActionHidden('replace');
    }
}
