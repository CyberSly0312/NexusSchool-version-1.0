<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\Guardian\Application\Actions\LinkGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipRevocationData;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipVerificationData;
use App\Domains\Guardian\Application\DTOs\RelationshipData;
use App\Domains\Guardian\Application\Services\GuardianRelationshipVerificationService;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\User\Enums\UserStatus;
use App\Filament\Parent\Resources\ParentChildResource;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

final class ParentChildResourceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private string $schoolId;

    private User $parent;

    private User $reviewer;

    private Guardian $guardian;

    private Student $child;

    private Student $sibling;

    private StudentGuardianRelationship $relationship;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        foreach (['guardian.link', 'guardian.verify', 'guardian.revoke'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->schoolId = makeTenant('Parent Panel School', 'active', 'parent-panel.nexusschool.test');

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

        $this->parent = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active,
        ]);
        $this->parent->assignRole('parent');

        $this->reviewer = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active,
        ]);
        $this->reviewer->assignRole('school_admin');
        $this->reviewer->givePermissionTo(['guardian.link', 'guardian.verify', 'guardian.revoke']);

        $this->guardian = Guardian::factory()->create([
            'school_id' => $this->schoolId,
            'user_id' => $this->parent->getKey(),
        ]);

        $this->child = Student::factory()->create(['school_id' => $this->schoolId]);
        $this->sibling = Student::factory()->create([
            'school_id' => $this->schoolId,
            'last_name' => $this->child->last_name,
        ]);

        $typeId = RelationshipType::query()
            ->forSchool($this->schoolId)
            ->where('code', 'guardian')
            ->value('id');

        $this->actingAs($this->reviewer);
        $this->relationship = app(LinkGuardianAction::class)->execute(new RelationshipData(
            schoolId: $this->schoolId,
            studentId: (int) $this->child->getKey(),
            guardianId: (int) $this->guardian->getKey(),
            relationshipTypeId: (int) $typeId,
            relationshipStartDate: now()->toDateString(),
        ));

        $this->verifyChild();

        Filament::setServingStatus();
        Filament::setCurrentPanel('parent');
        $this->actingAs($this->parent);
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            Tenancy::end();
        }

        parent::tearDown();
    }

    public function test_list_only_contains_the_explicitly_authorized_child(): void
    {
        $this->get(ParentChildResource::getUrl('index'))
            ->assertSuccessful()
            ->assertSee($this->child->student_number)
            ->assertDontSee($this->sibling->student_number);
    }

    public function test_authorized_child_view_is_rendered(): void
    {
        $this->get(ParentChildResource::getUrl('view', ['record' => $this->child->getKey()]))
            ->assertSuccessful()
            ->assertSee($this->child->student_number);
    }

    public function test_crafted_related_and_unrelated_record_ids_are_forbidden(): void
    {
        $unverifiedStudent = Student::factory()->create(['school_id' => $this->schoolId]);

        foreach ([$this->sibling->getKey(), $unverifiedStudent->getKey(), 999999999] as $recordId) {
            $this->get(ParentChildResource::getUrl('view', ['record' => $recordId]))
                ->assertForbidden();
        }
    }

    public function test_revocation_removes_the_child_from_list_and_view(): void
    {
        $this->revokeChild();

        $this->get(ParentChildResource::getUrl('index'))
            ->assertSuccessful()
            ->assertDontSee($this->child->student_number);

        $this->get(ParentChildResource::getUrl('view', ['record' => $this->child->getKey()]))
            ->assertForbidden();
    }

    public function test_a_cross_school_child_is_forbidden(): void
    {
        $otherSchoolId = makeTenant('Other Panel School', 'active', 'parent-panel-other.nexusschool.test');
        $foreignStudent = Student::factory()->create(['school_id' => $otherSchoolId]);

        $this->get(ParentChildResource::getUrl('view', ['record' => $foreignStudent->getKey()]))
            ->assertForbidden();
    }

    public function test_a_non_parent_user_cannot_reach_the_child_data_surface(): void
    {
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active,
        ]);
        $teacher->assignRole('teacher');

        $this->actingAs($teacher)
            ->get(ParentChildResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_an_inactive_parent_cannot_reach_the_child_data_surface(): void
    {
        $this->parent->update(['status' => UserStatus::Suspended]);

        $this->get(ParentChildResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_a_parent_cannot_use_the_staff_review_service(): void
    {
        $this->actingAs($this->parent);

        try {
            app(GuardianRelationshipVerificationService::class)->verifyAndAuthorize(
                GuardianRelationshipVerificationData::fromRelationship($this->relationship),
            );
            $this->fail('The parent was allowed to approve their own relationship.');
        } catch (\App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException $exception) {
            $this->assertSame(
                \App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason::ActorNotAuthorized,
                $exception->reason,
            );
        }
    }

    private function verifyChild(): void
    {
        $this->actingAs($this->reviewer);
        $service = app(GuardianRelationshipVerificationService::class);
        $data = GuardianRelationshipVerificationData::fromRelationship($this->relationship);

        $service->request($data);
        $service->verifyAndAuthorize($data);
    }

    private function revokeChild(): void
    {
        $this->actingAs($this->reviewer);
        app(GuardianRelationshipVerificationService::class)->revoke(
            GuardianRelationshipRevocationData::fromRelationship($this->relationship),
        );

        $this->actingAs($this->parent);
    }
}
