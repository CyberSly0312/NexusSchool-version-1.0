<?php

declare(strict_types=1);

use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'transfer', 'suspend', 'graduate', 'archive', 'restore'] as $action) {
        Permission::firstOrCreate(['name' => "student.{$action}", 'guard_name' => 'web']);
    }
});

function studentGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'student-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

test('user with the view permission can list students', function (): void {
    $user = studentGrantPermission('student.view', $this->schoolId);

    expect($user->can('viewAny', Student::class))->toBeTrue();
});

test('user without permission cannot list students', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Student::class))->toBeFalse();
});

test('user can view only students of their own school', function (): void {
    $ownSchool = makeTenant();
    $otherSchool = makeTenant();

    $student = Student::factory()->create(['school_id' => $ownSchool]);

    $owner = studentGrantPermission('student.view', $ownSchool);
    $outsider = studentGrantPermission('student.view', $otherSchool);

    expect($owner->can('view', $student))->toBeTrue()
        ->and($outsider->can('view', $student))->toBeFalse();
});

test('user needs the create permission to register a student', function (): void {
    $withCreate = studentGrantPermission('student.create', $this->schoolId);
    $withViewOnly = studentGrantPermission('student.view', $this->schoolId);

    expect($withCreate->can('create', Student::class))->toBeTrue()
        ->and($withViewOnly->can('create', Student::class))->toBeFalse();
});

test('lifecycle actions require their dedicated permission', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    $withTransfer = studentGrantPermission('student.transfer', $this->schoolId);
    $withViewOnly = studentGrantPermission('student.view', $this->schoolId);

    expect($withTransfer->can('transfer', $student))->toBeTrue()
        ->and($withViewOnly->can('transfer', $student))->toBeFalse()
        ->and($withTransfer->can('suspend', $student))->toBeFalse();
});

test('super_admin bypasses school scoping and lifecycle permissions', function (): void {
    $otherSchool = makeTenant();

    $student = Student::factory()->create(['school_id' => $otherSchool]);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $student))->toBeTrue()
        ->and($superAdmin->can('graduate', $student))->toBeTrue();
});

test('restore is only allowed with the dedicated permission', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    $withRestore = studentGrantPermission('student.restore', $this->schoolId);
    $withViewOnly = studentGrantPermission('student.view', $this->schoolId);

    expect($withRestore->can('restore', $student))->toBeTrue()
        ->and($withViewOnly->can('restore', $student))->toBeFalse();
});

test('forceDelete is always denied', function (): void {
    $user = studentGrantPermission('student.delete', $this->schoolId);
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('forceDelete', $student))->toBeFalse();
});
