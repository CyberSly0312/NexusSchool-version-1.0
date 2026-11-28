<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\Pages\ListStudentGuardianRelationships;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

/**
 * Real Filament entry points for the Task 11.3 review lifecycle. These tests
 * exercise the table actions, not only the underlying application service.
 */
final class GuardianRelationshipReviewActionsTest extends TestCase
{
    use RefreshDatabase;

    private string $schoolId;

    private User $reviewer;

    private User $parent;

    private User $limitedStaff;

    private StudentGuardianRelationship $relationship;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['parent', 'school_admin', 'teacher'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->schoolId = makeTenant('Review Actions School', 'active', 'review-actions.nexusschool.test');

        RelationshipType::create([
            'school_id' => $this->schoolId,
            'code' => 'guardian',
            'label' => 'Tuteur',
            'category' => 'family',
            'is_active' => true,
        ]);

        if (tenancy()->initialized) {
            Tenancy::end();
        }
        Tenancy::initialize(School::query()->findOrFail($this->schoolId));

        $this->reviewer = $this->makeUser('school_admin', ['guardian.view', 'guardian.link', 'guardian.verify', 'guardian.revoke']);
        $this->parent = $this->makeUser('parent', ['guardian.view']);
        $this->limitedStaff = $this->makeUser('teacher', ['guardian.view']);

        $guardian = Guardian::factory()->create([
            'school_id' => $this->schoolId,
            'user_id' => $this->parent->getKey(),
        ]);

        $student = Student::factory()->create(['school_id' => $this->schoolId]);
        $typeId = RelationshipType::query()
            ->forSchool($this->schoolId)
            ->where('code', 'guardian')
            ->value('id');

        $this->actingAs($this->reviewer);
        $this->relationship = app(LinkGuardianAction::class)->execute(new RelationshipData(
            schoolId: $this->schoolId,
            studentId: (int) $student->getKey(),
            guardianId: (int) $guardian->getKey(),
            relationshipTypeId: (int) $typeId,
            relationshipStartDate: now()->toDateString(),
        ));

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
        $this->actingAs($this->reviewer);
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            Tenancy::end();
        }

        parent::tearDown();
    }

    public function test_table_actions_drive_request_approval_and_revocation(): void
    {
        $table = Livewire::test(ListStudentGuardianRelationships::class);

        $table->assertTableActionVisible('request_portal_access', $this->relationship)
            ->assertTableActionVisible('approve_portal_access', $this->relationship)
            ->assertTableActionHidden('revoke_portal_access', $this->relationship)
            ->callTableAction('request_portal_access', $this->relationship);

        expect($this->relationship->fresh()->verification_status)->toBe(RelationshipVerificationStatus::Unverified)
            ->and($this->relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::NotAuthorized)
            ->and(StudentGuardianRelationshipHistory::query()
                ->where('student_guardian_id', $this->relationship->getKey())
                ->where('event', 'portal_access_requested')
                ->where('user_id', $this->reviewer->getKey())
                ->exists())->toBeTrue();

        $table->callTableAction('approve_portal_access', $this->relationship);

        expect($this->relationship->fresh()->verification_status)->toBe(RelationshipVerificationStatus::Verified)
            ->and($this->relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::Authorized);

        $table->callTableAction('revoke_portal_access', $this->relationship);

        expect($this->relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::Revoked)
            ->and(StudentGuardianRelationshipHistory::query()
                ->where('student_guardian_id', $this->relationship->getKey())
                ->where('event', 'portal_access_revoked')
                ->where('user_id', $this->reviewer->getKey())
                ->exists())->toBeTrue();
    }

    public function test_review_actions_are_hidden_from_parent_and_unauthorized_staff(): void
    {
        foreach ([$this->parent, $this->limitedStaff] as $actor) {
            $this->actingAs($actor);

            Livewire::test(ListStudentGuardianRelationships::class)
                ->assertTableActionHidden('request_portal_access', $this->relationship)
                ->assertTableActionHidden('approve_portal_access', $this->relationship)
                ->assertTableActionHidden('reject_portal_access', $this->relationship)
                ->assertTableActionHidden('revoke_portal_access', $this->relationship)
                ->mountTableAction('approve_portal_access', $this->relationship)
                ->callMountedTableAction();

            expect($this->relationship->fresh()->verification_status)->toBe(RelationshipVerificationStatus::Unverified)
                ->and($this->relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::NotAuthorized);
        }
    }

    public function test_generic_delete_is_replaced_by_audited_unlink(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active,
        ]);
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);
        Livewire::test(ListStudentGuardianRelationships::class)
            ->callTableAction('unlink_relationship', $this->relationship);

        expect($this->relationship->fresh()->trashed())->toBeTrue()
            ->and($this->relationship->fresh()->portal_access_status)->toBe(RelationshipPortalAccessStatus::Revoked)
            ->and(StudentGuardianRelationshipHistory::query()
                ->where('student_guardian_id', $this->relationship->getKey())
                ->where('event', 'unlinked')
                ->where('user_id', $superAdmin->getKey())
                ->exists())->toBeTrue()
            ->and(StudentGuardianRelationshipHistory::query()
                ->where('student_guardian_id', $this->relationship->getKey())
                ->where('event', 'linked')
                ->exists())->toBeTrue();
    }

    /**
     * @param  list<string>  $permissions
     */
    private function makeUser(string $role, array $permissions): User
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active,
        ]);
        $user->assignRole($role);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
