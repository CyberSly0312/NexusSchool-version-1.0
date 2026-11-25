<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\School\Models\School;
use App\Domains\System\Enums\AuditAction;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class SchoolAdminRoleAssignmentSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var list<string>
     */
    private const ALL_ROLES = [
        'super_admin',
        'school_admin',
        'director',
        'vice_director',
        'secretary',
        'accountant',
        'teacher',
        'librarian',
        'registrar',
        'student',
        'parent',
    ];

    protected string $schoolId;
    protected string $otherSchoolId;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = makeTenant('Role Security School', 'active', 'role-security.nexusschool.test');
        $this->otherSchoolId = makeTenant('Other School', 'active', 'role-security-other.nexusschool.test');
        useTenantHost($this->schoolId);

        foreach (self::ALL_ROLES as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        Role::firstOrCreate(['name' => 'external_tool', 'guard_name' => 'api']);

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $schoolAdminRole = $this->role('school_admin');
        $schoolAdminRole->syncPermissions(Permission::query()->get());

        $this->admin = makeRoleUser($this->schoolId, 'school_admin');
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
        Tenancy::initialize(School::query()->findOrFail($this->schoolId));
    }

    public function test_school_admin_can_assign_every_approved_role(): void
    {
        foreach (SchoolAdminRolePolicy::ALLOWED_ROLES as $roleName) {
            $target = $this->target();
            $manager = $this->managerFor($target);

            $this->attach($manager, $this->role($roleName))->assertHasNoActionErrors();

            $this->assertTrue($target->fresh()->hasRole($roleName));
            $this->assertSame(1, $this->pivotCount($target, $this->role($roleName)));
        }
    }

    public function test_school_admin_cannot_assign_any_forbidden_role(): void
    {
        foreach (array_diff(self::ALL_ROLES, SchoolAdminRolePolicy::ALLOWED_ROLES) as $roleName) {
            $target = $this->target();
            $manager = $this->managerFor($target);

            $this->attach($manager, $this->role($roleName))->assertHasActionErrors(['recordId']);

            $this->assertFalse($target->fresh()->hasRole($roleName));
            $this->assertSame(0, $this->pivotCount($target, $this->role($roleName)));
        }
    }

    public function test_crafted_livewire_request_cannot_bypass_the_allowlist(): void
    {
        $target = $this->target();
        $manager = $this->managerFor($target);

        $manager->callTableAction(AttachAction::class, data: [
            'recordId' => $this->role('school_admin')->getKey(),
            'role' => 'teacher',
            'role_id' => $this->role('teacher')->getKey(),
            'school_id' => $this->schoolId,
        ])->assertHasActionErrors(['recordId']);

        $this->assertSame([], $target->fresh()->getRoleNames()->all());
        $this->assertSame(0, $this->pivotCount($target, $this->role('school_admin')));
    }

    public function test_invalid_and_wrong_guard_role_ids_are_rejected(): void
    {
        $target = $this->target();

        $this->attach($this->managerFor($target), 999999999)->assertHasActionErrors(['recordId']);
        $this->attach(
            $this->managerFor($target),
            Role::query()->where('name', 'external_tool')->where('guard_name', 'api')->firstOrFail(),
        )->assertHasActionErrors(['recordId']);

        $this->assertSame([], $target->fresh()->getRoleNames()->all());
    }

    public function test_duplicate_assignment_is_handled_safely(): void
    {
        $target = $this->target('teacher');
        $role = $this->role('teacher');

        $this->attach($this->managerFor($target), $role)->assertHasNoActionErrors();

        $this->assertSame(1, $this->pivotCount($target, $role));
        $this->assertSame(['teacher'], $target->fresh()->getRoleNames()->all());
    }

    public function test_school_admin_cannot_modify_a_user_from_another_school(): void
    {
        $foreignTarget = makeRoleUser($this->otherSchoolId, 'teacher');
        $before = $foreignTarget->getRoleNames()->all();

        $this->managerFor($foreignTarget)->assertStatus(403);

        $this->assertSame($before, $foreignTarget->fresh()->getRoleNames()->all());
    }

    public function test_school_admin_cannot_manipulate_target_school_identifier(): void
    {
        $target = $this->target();
        $manager = $this->managerFor($target);

        $this->attach($manager, $this->role('teacher'), [
            'school_id' => $this->otherSchoolId,
        ])->assertHasNoActionErrors();

        $this->assertTrue($target->fresh()->hasRole('teacher'));
        $this->assertSame(1, AuditLog::query()->where('action', AuditAction::RoleAssigned->value)->count());
        $this->assertSame(
            $this->schoolId,
            AuditLog::query()->where('action', AuditAction::RoleAssigned->value)->value('school_id'),
        );
        $this->assertSame(0, AuditLog::query()->where('action', AuditAction::RoleChangeRejected->value)->count());
    }

    public function test_missing_tenant_context_fails_closed(): void
    {
        $target = $this->target('teacher');
        $before = $target->getRoleNames()->all();

        Tenancy::end();

        $this->managerFor($target)->assertStatus(403);

        $this->assertSame($before, $target->fresh()->getRoleNames()->all());
    }

    public function test_soft_deleted_target_is_rejected(): void
    {
        $target = $this->target();
        $target->delete();

        $this->managerFor($target->fresh())->assertStatus(403);
    }

    public function test_school_admin_can_remove_an_approved_role(): void
    {
        $target = $this->target('teacher');
        $role = $this->role('teacher');

        $this->detach($this->managerFor($target), $role)->assertHasNoActionErrors();

        $this->assertSame(0, $this->pivotCount($target, $role));
    }

    public function test_school_admin_cannot_remove_a_role_outside_the_allowlist(): void
    {
        $target = $this->target('director');
        $role = $this->role('director');

        $this->detach($this->managerFor($target), $role)->assertActionHalted();

        $this->assertSame(1, $this->pivotCount($target, $role));
        $this->assertSame(1, AuditLog::query()->where('action', AuditAction::RoleChangeRejected->value)->count());
    }

    public function test_unauthorized_bulk_removal_is_rejected_atomically(): void
    {
        $target = $this->target('teacher');
        $target->assignRole('director');
        $teacher = $this->role('teacher');
        $director = $this->role('director');

        $this->bulkDetach($this->managerFor($target), [$teacher, $director])->assertActionHalted();

        $this->assertSame(1, $this->pivotCount($target, $teacher));
        $this->assertSame(1, $this->pivotCount($target, $director));
    }

    public function test_last_active_school_admin_cannot_be_removed(): void
    {
        $this->admin->removeRole('school_admin');

        $superAdmin = makeRoleUser($this->schoolId, 'super_admin');
        $this->actingAs($superAdmin);

        $target = makeRoleUser($this->schoolId, 'school_admin');
        $role = $this->role('school_admin');

        $this->detach($this->managerFor($target), $role)->assertActionHalted();

        $this->assertSame(1, $this->pivotCount($target, $role));
        $this->assertTrue($target->fresh()->hasRole('school_admin'));
    }

    public function test_self_removal_cannot_bypass_the_administrative_lifecycle(): void
    {
        $role = $this->role('school_admin');

        $this->detach($this->managerFor($this->admin), $role)->assertActionHalted();

        $this->assertSame(1, $this->pivotCount($this->admin, $role));
        $this->assertTrue($this->admin->fresh()->hasRole('school_admin'));
    }

    public function test_successful_assignment_creates_complete_audit_record(): void
    {
        $target = $this->target();
        $role = $this->role('teacher');

        $this->attach($this->managerFor($target), $role)->assertHasNoActionErrors();

        $audit = $this->auditFor(AuditAction::RoleAssigned);

        $this->assertSame($this->admin->getKey(), $audit->user_id);
        $this->assertSame(User::class, $audit->user_type);
        $this->assertSame(User::class, $audit->auditable_type);
        $this->assertSame((string) $target->getKey(), $audit->auditable_id);
        $this->assertSame($this->schoolId, $audit->school_id);
        $this->assertNotNull($audit->created_at);
        $this->assertSame([
            'actor_user_id' => (string) $this->admin->getKey(),
            'target_user_id' => (string) $target->getKey(),
            'role_id' => (string) $role->getKey(),
            'role_name' => 'teacher',
            'school_id' => $this->schoolId,
            'operation' => 'assign',
            'outcome' => 'assigned',
        ], $audit->new_values);
    }

    public function test_successful_removal_creates_complete_audit_record(): void
    {
        $target = $this->target('secretary');
        $role = $this->role('secretary');

        $this->detach($this->managerFor($target), $role)->assertHasNoActionErrors();

        $audit = $this->auditFor(AuditAction::RoleRemoved);

        $this->assertSame($this->admin->getKey(), $audit->user_id);
        $this->assertSame((string) $target->getKey(), $audit->auditable_id);
        $this->assertSame($this->schoolId, $audit->school_id);
        $this->assertSame([
            'actor_user_id' => (string) $this->admin->getKey(),
            'target_user_id' => (string) $target->getKey(),
            'role_id' => (string) $role->getKey(),
            'role_name' => 'secretary',
            'school_id' => $this->schoolId,
            'operation' => 'remove',
            'outcome' => 'removed',
        ], $audit->new_values);
    }

    public function test_audit_records_never_contain_secrets(): void
    {
        $target = $this->target('accountant');
        $target->update(['password' => bcrypt('mot-de-passe-tres-secret-10B')]);

        $this->detach($this->managerFor($target), $this->role('accountant'))->assertHasNoActionErrors();

        foreach (AuditLog::query()->get() as $audit) {
            $serialized = json_encode($audit->toArray());
            $this->assertStringNotContainsString('mot-de-passe-tres-secret-10B', $serialized);
            $this->assertStringNotContainsString('"password"', $serialized);
            $this->assertStringNotContainsString('invitation_token', $serialized);
            $this->assertStringNotContainsString('reset_token', $serialized);
        }
    }

    public function test_relation_manager_mounts_with_installed_filament_api(): void
    {
        $target = $this->target('teacher');

        $component = $this->managerFor($target)->assertSuccessful();
        $table = $component->instance()->getTable();

        $this->assertInstanceOf(AttachAction::class, $table->getHeaderActions()[0]);
        $this->assertNotInstanceOf(AssociateAction::class, $table->getHeaderActions()[0]);
        $this->assertInstanceOf(DetachAction::class, $table->getActions()[0]);
        $this->assertNotInstanceOf(DissociateAction::class, $table->getActions()[0]);
        $this->assertInstanceOf(DetachBulkAction::class, $table->getBulkActions()[0]);
        $this->assertContainsOnlyInstancesOf(AssociateAction::class, []);
    }

    public function test_relationship_action_uses_record_id_and_service_callback_api(): void
    {
        $source = (string) file_get_contents((new \ReflectionClass(RolesRelationManager::class))->getFileName());

        $this->assertStringContainsString('recordSelectOptionsQuery', $source);
        $this->assertStringContainsString('->using(', $source);
        $this->assertStringNotContainsString('associateRelatedRecordUsing', $source);
        $this->assertStringNotContainsString("['role_id']", $source);

        $target = $this->target();
        $this->attach($this->managerFor($target), $this->role('teacher'))->assertHasNoActionErrors();

        $this->assertTrue($target->fresh()->hasRole('teacher'));
    }

    private function managerFor(User $owner): Testable
    {
        return Livewire::test(RolesRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => EditUser::class,
        ]);
    }

    private function target(string $initialRole = ''): User
    {
        $user = User::factory()->create(['school_id' => $this->schoolId]);

        if ($initialRole !== '') {
            $user->assignRole($initialRole);
        }

        return $user;
    }

    private function role(string $name): Role
    {
        return Role::query()
            ->where('name', $name)
            ->where('guard_name', 'web')
            ->firstOrFail();
    }

    private function attach(Testable $manager, int|string|Role $role, array $extraData = []): Testable
    {
        $roleId = $role instanceof Role ? $role->getKey() : $role;

        return $manager->callTableAction(AttachAction::class, data: [
            'recordId' => $roleId,
            ...$extraData,
        ]);
    }

    private function detach(Testable $manager, Role $role): Testable
    {
        return $manager->callTableAction(DetachAction::class, record: $role);
    }

    /**
     * @param  list<Role>  $roles
     */
    private function bulkDetach(Testable $manager, array $roles): Testable
    {
        return $manager->callTableBulkAction(DetachBulkAction::class, $roles);
    }

    private function pivotCount(User $user, Role $role): int
    {
        return (int) DB::table('model_has_roles')
            ->where('model_type', $user->getMorphClass())
            ->where('model_id', $user->getKey())
            ->where('role_id', $role->getKey())
            ->count();
    }

    private function auditFor(AuditAction $action): AuditLog
    {
        return AuditLog::query()
            ->where('action', $action->value)
            ->firstOrFail();
    }
}
