<?php

declare(strict_types=1);

use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'assign', 'transfer', 'terminate', 'suspend', 'restore', 'teach'] as $action) {
        Permission::firstOrCreate(['name' => "staff.{$action}", 'guard_name' => 'web']);
    }
});

function staffGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'staff-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

test('user with the view permission can list staff', function (): void {
    $user = staffGrantPermission('staff.view', $this->schoolId);

    expect($user->can('viewAny', Staff::class))->toBeTrue();
});

test('user without permission cannot list staff', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Staff::class))->toBeFalse();
});

test('user can view only staff of their own school', function (): void {
    $ownSchool = makeTenant();
    $otherSchool = makeTenant();

    $staff = Staff::factory()->create(['school_id' => $ownSchool]);

    $owner = staffGrantPermission('staff.view', $ownSchool);
    $outsider = staffGrantPermission('staff.view', $otherSchool);

    expect($owner->can('view', $staff))->toBeTrue()
        ->and($outsider->can('view', $staff))->toBeFalse();
});

test('user needs the create permission to register a staff member', function (): void {
    $withCreate = staffGrantPermission('staff.create', $this->schoolId);
    $withViewOnly = staffGrantPermission('staff.view', $this->schoolId);

    expect($withCreate->can('create', Staff::class))->toBeTrue()
        ->and($withViewOnly->can('create', Staff::class))->toBeFalse();
});

test('workforce actions require their dedicated permission', function (): void {
    $staff = Staff::factory()->create(['school_id' => $this->schoolId]);

    $withAssign = staffGrantPermission('staff.assign', $this->schoolId);
    $withViewOnly = staffGrantPermission('staff.view', $this->schoolId);

    expect($withAssign->can('assign', $staff))->toBeTrue()
        ->and($withViewOnly->can('assign', $staff))->toBeFalse()
        ->and($withAssign->can('terminate', $staff))->toBeFalse();
});

test('suspend requires its dedicated permission on the staff member', function (): void {
    $staff = Staff::factory()->create(['school_id' => $this->schoolId]);

    $withSuspend = staffGrantPermission('staff.suspend', $this->schoolId);
    $withViewOnly = staffGrantPermission('staff.view', $this->schoolId);

    expect($withSuspend->can('suspend', $staff))->toBeTrue()
        ->and($withViewOnly->can('suspend', $staff))->toBeFalse();
});

test('teaching subjects assignment requires the teach permission', function (): void {
    $staff = Staff::factory()->teacher()->create(['school_id' => $this->schoolId]);

    $withTeach = staffGrantPermission('staff.teach', $this->schoolId);
    $withViewOnly = staffGrantPermission('staff.view', $this->schoolId);

    expect($withTeach->can('teach', $staff))->toBeTrue()
        ->and($withViewOnly->can('teach', $staff))->toBeFalse();
});

test('workforce permissions are school scoped', function (): void {
    $otherSchool = makeTenant();

    $staff = Staff::factory()->create(['school_id' => $this->schoolId]);
    $outsider = staffGrantPermission('staff.suspend', $otherSchool);

    expect($outsider->can('suspend', $staff))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();

    $staff = Staff::factory()->create(['school_id' => $otherSchool]);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $staff))->toBeTrue()
        ->and($superAdmin->can('assign', $staff))->toBeTrue();
});

test('forceDelete is always denied', function (): void {
    $user = staffGrantPermission('staff.delete', $this->schoolId);
    $staff = Staff::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('forceDelete', $staff))->toBeFalse();
});
