<?php

declare(strict_types=1);

use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'calculate', 'validate', 'lock', 'unlock', 'publish'] as $action) {
        Permission::firstOrCreate(['name' => "gradebook.{$action}", 'guard_name' => 'web']);
    }
});

function gradebookGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'gb-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function gradebookFor(string $schoolId): Gradebook
{
    return Gradebook::factory()->forSchool($schoolId)->create();
}

test('user with the view permission can list gradebooks', function (): void {
    $user = gradebookGrantPermission('gradebook.view', $this->schoolId);

    expect($user->can('viewAny', Gradebook::class))->toBeTrue();
});

test('user without permission cannot list gradebooks', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Gradebook::class))->toBeFalse();
});

test('gradebook access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $gradebook = gradebookFor($this->schoolId);

    $owner = gradebookGrantPermission('gradebook.view', $this->schoolId);
    $outsider = gradebookGrantPermission('gradebook.view', $otherSchool);

    expect($owner->can('view', $gradebook))->toBeTrue()
        ->and($outsider->can('view', $gradebook))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $gradebook = gradebookFor($this->schoolId);

    $withCreate = gradebookGrantPermission('gradebook.create', $this->schoolId);
    $withUpdate = gradebookGrantPermission('gradebook.update', $this->schoolId);
    $withLock = gradebookGrantPermission('gradebook.lock', $this->schoolId);
    $withUnlock = gradebookGrantPermission('gradebook.unlock', $this->schoolId);

    expect($withCreate->can('create', Gradebook::class))->toBeTrue()
        ->and($withCreate->can('update', $gradebook))->toBeFalse()
        ->and($withUpdate->can('update', $gradebook))->toBeTrue()
        ->and($withLock->can('lock', $gradebook))->toBeTrue()
        ->and($withLock->can('publish', $gradebook))->toBeFalse()
        ->and($withUnlock->can('unlock', $gradebook))->toBeTrue();
});

test('calculation, validation and publishing need their own capabilities', function (): void {
    $gradebook = gradebookFor($this->schoolId);

    $withCalculate = gradebookGrantPermission('gradebook.calculate', $this->schoolId);
    $withValidate = gradebookGrantPermission('gradebook.validate', $this->schoolId);
    $withPublish = gradebookGrantPermission('gradebook.publish', $this->schoolId);

    expect($withCalculate->can('calculate', $gradebook))->toBeTrue()
        ->and($withCalculate->can('publish', $gradebook))->toBeFalse()
        ->and($withValidate->can('validate', $gradebook))->toBeTrue()
        ->and($withValidate->can('lock', $gradebook))->toBeFalse()
        ->and($withPublish->can('publish', $gradebook))->toBeTrue();
});

test('gradebook capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $gradebook = gradebookFor($this->schoolId);

    $outsider = gradebookGrantPermission('gradebook.publish', $otherSchool);

    expect($outsider->can('publish', $gradebook))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $gradebook = gradebookFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $gradebook))->toBeTrue()
        ->and($superAdmin->can('calculate', $gradebook))->toBeTrue()
        ->and($superAdmin->can('publish', $gradebook))->toBeTrue();
});

test('gradebook aggregates are never deleted, restored or force deleted', function (): void {
    $gradebook = gradebookFor($this->schoolId);

    $user = gradebookGrantPermission('gradebook.delete', $this->schoolId);

    expect($user->can('delete', $gradebook))->toBeFalse()
        ->and($user->can('restore', $gradebook))->toBeFalse()
        ->and($user->can('forceDelete', $gradebook))->toBeFalse()
        ->and($user->can('viewAny', \App\Domains\Gradebook\Infrastructure\Models\GradeEntry::class))->toBeFalse();
});
