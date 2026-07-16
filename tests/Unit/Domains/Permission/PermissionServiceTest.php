<?php

namespace Tests\Unit\Domains\Permission;

use App\Domains\Permission\DTOs\PermissionData;
use App\Domains\Permission\Events\PermissionCreated;
use App\Domains\Permission\Events\PermissionUpdated;
use App\Domains\Permission\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PermissionService;
    }

    public function test_can_create_permission(): void
    {
        $data = PermissionData::fromArray([
            'name' => 'school.view',
        ]);

        $permission = $this->service->create($data);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('school.view', $permission->name);
        $this->assertEquals('web', $permission->guard_name);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::create(['name' => 'old.permission', 'guard_name' => 'web']);

        $updated = $this->service->update($permission, ['name' => 'new.permission']);

        $this->assertEquals('new.permission', $updated->name);
    }

    public function test_can_delete_non_system_permission(): void
    {
        $permission = Permission::create(['name' => 'school.create', 'guard_name' => 'web']);

        $deleted = $this->service->delete($permission);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('permissions', ['name' => 'school.create']);
    }

    public function test_cannot_delete_system_permission(): void
    {
        $permission = Permission::create(['name' => 'permission.create', 'guard_name' => 'web']);

        $deleted = $this->service->delete($permission);

        $this->assertFalse($deleted);
        $this->assertDatabaseHas('permissions', ['name' => 'permission.create']);
    }

    public function test_can_assign_permission_to_role(): void
    {
        $permission = Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'teacher', 'guard_name' => 'web']);

        $this->service->assignToRole($permission, $role);

        $this->assertTrue($role->hasPermissionTo('school.view'));
    }

    public function test_can_revoke_permission_from_role(): void
    {
        $permission = Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->service->revokeFromRole($permission, $role);

        $role->refresh();
        $this->assertFalse($role->hasPermissionTo('school.view'));
    }

    public function test_can_sync_role_permissions(): void
    {
        $perm1 = Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'school.create', 'guard_name' => 'web']);
        $perm3 = Permission::create(['name' => 'school.update', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $role->givePermissionTo($perm1);

        $this->service->syncRolePermissions($role, [$perm2->id, $perm3->id]);

        $role->refresh();
        $this->assertFalse($role->hasPermissionTo('school.view'));
        $this->assertTrue($role->hasPermissionTo('school.create'));
        $this->assertTrue($role->hasPermissionTo('school.update'));
    }

    public function test_can_find_permission_by_name(): void
    {
        Permission::create(['name' => 'school.view', 'guard_name' => 'web']);

        $permission = $this->service->findByName('school.view');

        $this->assertEquals('school.view', $permission->name);
    }

    public function test_can_paginate_permissions(): void
    {
        Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'school.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'user.view', 'guard_name' => 'web']);

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_permissions(): void
    {
        Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'user.view', 'guard_name' => 'web']);

        $result = $this->service->paginate(['search' => 'school'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_permissions_by_group(): void
    {
        Permission::create(['name' => 'school.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'school.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'user.view', 'guard_name' => 'web']);

        $result = $this->service->paginate(['group' => 'school'], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_is_system_permission(): void
    {
        $systemPerm = Permission::create(['name' => 'permission.create', 'guard_name' => 'web']);
        $customPerm = Permission::create(['name' => 'school.view', 'guard_name' => 'web']);

        $this->assertTrue($this->service->isSystemPermission($systemPerm));
        $this->assertFalse($this->service->isSystemPermission($customPerm));
    }

    public function test_get_default_permissions_returns_all_groups(): void
    {
        $permissions = $this->service->getDefaultPermissions();

        $this->assertCount(84, $permissions); // 21 groups × 4 CRUD
        $this->assertArrayHasKey('name', $permissions[0]);
    }

    public function test_extract_group_name(): void
    {
        $this->assertEquals('school', $this->service->extractGroupName('school.view'));
        $this->assertEquals('audit_log', $this->service->extractGroupName('audit_log.create'));
    }

    public function test_events_are_dispatched_on_create(): void
    {
        Event::fake([PermissionCreated::class]);

        $data = PermissionData::fromArray(['name' => 'school.view']);
        $this->service->create($data);

        Event::assertDispatched(PermissionCreated::class);
    }

    public function test_events_are_dispatched_on_update(): void
    {
        $permission = Permission::create(['name' => 'old.permission', 'guard_name' => 'web']);

        Event::fake([PermissionUpdated::class]);

        $this->service->update($permission, ['name' => 'new.permission']);

        Event::assertDispatched(PermissionUpdated::class);
    }
}
