<?php

declare(strict_types=1);

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'promote', 'repeat', 'transfer', 'graduate', 'withdraw', 'suspend', 'restore'] as $action) {
        Permission::firstOrCreate(['name' => "enrollment.{$action}", 'guard_name' => 'web']);
    }
});

function enrollmentGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'enrollment-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

test('user with the view permission can list enrollments', function (): void {
    $user = enrollmentGrantPermission('enrollment.view', $this->schoolId);

    expect($user->can('viewAny', Enrollment::class))->toBeTrue();
});

test('user without permission cannot list enrollments', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Enrollment::class))->toBeFalse();
});

test('user can view only enrollments of their own school', function (): void {
    $ownSchool = makeTenant();
    $otherSchool = makeTenant();

    $enrollment = Enrollment::factory()->create(['school_id' => $ownSchool]);

    $owner = enrollmentGrantPermission('enrollment.view', $ownSchool);
    $outsider = enrollmentGrantPermission('enrollment.view', $otherSchool);

    expect($owner->can('view', $enrollment))->toBeTrue()
        ->and($outsider->can('view', $enrollment))->toBeFalse();
});

test('user needs the create permission to enroll a student', function (): void {
    $withCreate = enrollmentGrantPermission('enrollment.create', $this->schoolId);
    $withViewOnly = enrollmentGrantPermission('enrollment.view', $this->schoolId);

    expect($withCreate->can('create', Enrollment::class))->toBeTrue()
        ->and($withViewOnly->can('create', Enrollment::class))->toBeFalse();
});

test('lifecycle actions require their dedicated permission', function (): void {
    $enrollment = Enrollment::factory()->create(['school_id' => $this->schoolId]);

    $withPromote = enrollmentGrantPermission('enrollment.promote', $this->schoolId);
    $withViewOnly = enrollmentGrantPermission('enrollment.view', $this->schoolId);

    expect($withPromote->can('promote', $enrollment))->toBeTrue()
        ->and($withPromote->can('repeat', $enrollment))->toBeFalse()
        ->and($withPromote->can('transfer', $enrollment))->toBeFalse()
        ->and($withPromote->can('graduate', $enrollment))->toBeFalse()
        ->and($withPromote->can('withdraw', $enrollment))->toBeFalse()
        ->and($withPromote->can('suspend', $enrollment))->toBeFalse()
        ->and($withViewOnly->can('promote', $enrollment))->toBeFalse();
});

test('withdraw and suspend each need their dedicated permission', function (): void {
    $enrollment = Enrollment::factory()->create(['school_id' => $this->schoolId]);

    $withWithdraw = enrollmentGrantPermission('enrollment.withdraw', $this->schoolId);
    $withSuspend = enrollmentGrantPermission('enrollment.suspend', $this->schoolId);

    expect($withWithdraw->can('withdraw', $enrollment))->toBeTrue()
        ->and($withWithdraw->can('suspend', $enrollment))->toBeFalse()
        ->and($withSuspend->can('suspend', $enrollment))->toBeTrue()
        ->and($withSuspend->can('withdraw', $enrollment))->toBeFalse();
});

test('super_admin bypasses school scoping and lifecycle permissions', function (): void {
    $otherSchool = makeTenant();

    $enrollment = Enrollment::factory()->create(['school_id' => $otherSchool]);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $enrollment))->toBeTrue()
        ->and($superAdmin->can('promote', $enrollment))->toBeTrue()
        ->and($superAdmin->can('graduate', $enrollment))->toBeTrue()
        ->and($superAdmin->can('update', $enrollment))->toBeTrue();
});

test('restore is only allowed with the dedicated permission', function (): void {
    $enrollment = Enrollment::factory()->suspended()->create(['school_id' => $this->schoolId]);

    $withRestore = enrollmentGrantPermission('enrollment.restore', $this->schoolId);
    $withViewOnly = enrollmentGrantPermission('enrollment.view', $this->schoolId);

    expect($withRestore->can('restore', $enrollment))->toBeTrue()
        ->and($withViewOnly->can('restore', $enrollment))->toBeFalse();
});

test('forceDelete is always denied', function (): void {
    $user = enrollmentGrantPermission('enrollment.delete', $this->schoolId);
    $enrollment = Enrollment::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('forceDelete', $enrollment))->toBeFalse();
});
