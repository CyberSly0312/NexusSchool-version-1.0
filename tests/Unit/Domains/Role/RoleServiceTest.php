<?php

namespace Tests\Unit\Domains\Role;

use App\Domains\Role\DTOs\RoleData;
use App\Domains\Role\Events\RoleCreated;
use App\Domains\Role\Events\RoleUpdated;
use App\Domains\Role\Services\RoleService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleService;
    }

    public function test_can_create_role(): void
    {
        $data = RoleData::fromArray([
            'name' => 'test_role',
        ]);

        $role = $this->service->create($data);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test_role', $role->name);
        $this->assertEquals('web', $role->guard_name);
    }

    public function test_can_update_role(): void
    {
        $role = Role::create(['name' => 'old_name', 'guard_name' => 'web']);

        $updated = $this->service->update($role, ['name' => 'new_name']);

        $this->assertEquals('new_name', $updated->name);
    }

    public function test_can_delete_non_system_role(): void
    {
        $role = Role::create(['name' => 'custom_role', 'guard_name' => 'web']);

        $deleted = $this->service->delete($role);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('roles', ['name' => 'custom_role']);
    }

    public function test_cannot_delete_system_role(): void
    {
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);

        $deleted = $this->service->delete($role);

        $this->assertFalse($deleted);
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
    }

    public function test_can_assign_role_to_user(): void
    {
        $role = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $this->service->assignToUser($user, $role);

        $this->assertTrue($user->hasRole('teacher'));
    }

    public function test_can_assign_role_by_name(): void
    {
        Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $this->service->assignToUser($user, 'teacher');

        $this->assertTrue($user->hasRole('teacher'));
    }

    public function test_can_revoke_role_from_user(): void
    {
        $role = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->service->revokeFromUser($user, $role);

        $this->assertFalse($user->hasRole('teacher'));
    }

    public function test_can_sync_user_roles(): void
    {
        $role1 = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'librarian', 'guard_name' => 'web']);
        $role3 = Role::create(['name' => 'student', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role1);

        $this->service->syncUserRoles($user, [$role2->id, $role3->id]);

        $this->assertFalse($user->hasRole('teacher'));
        $this->assertTrue($user->hasRole('librarian'));
        $this->assertTrue($user->hasRole('student'));
    }

    public function test_can_find_role_by_name(): void
    {
        Role::create(['name' => 'teacher', 'guard_name' => 'web']);

        $role = $this->service->findByName('teacher');

        $this->assertEquals('teacher', $role->name);
    }

    public function test_can_paginate_roles(): void
    {
        Role::create(['name' => 'role_a', 'guard_name' => 'web']);
        Role::create(['name' => 'role_b', 'guard_name' => 'web']);
        Role::create(['name' => 'role_c', 'guard_name' => 'web']);

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_roles(): void
    {
        Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        Role::create(['name' => 'student', 'guard_name' => 'web']);

        $result = $this->service->paginate(['search' => 'teach'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_is_system_role(): void
    {
        $systemRole = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $customRole = Role::create(['name' => 'custom_role', 'guard_name' => 'web']);

        $this->assertTrue($this->service->isSystemRole($systemRole));
        $this->assertFalse($this->service->isSystemRole($customRole));
    }

    public function test_get_default_roles_returns_all_system_roles(): void
    {
        $roles = $this->service->getDefaultRoles();

        $this->assertCount(11, $roles);
        $this->assertArrayHasKey('name', $roles[0]);
    }

    public function test_events_are_dispatched_on_create(): void
    {
        Event::fake([RoleCreated::class]);

        $data = RoleData::fromArray(['name' => 'test_role']);
        $this->service->create($data);

        Event::assertDispatched(RoleCreated::class);
    }

    public function test_events_are_dispatched_on_update(): void
    {
        $role = Role::create(['name' => 'old_name', 'guard_name' => 'web']);

        Event::fake([RoleUpdated::class]);

        $this->service->update($role, ['name' => 'new_name']);

        Event::assertDispatched(RoleUpdated::class);
    }
}
