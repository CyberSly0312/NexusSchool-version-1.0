<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete'] as $action) {
        Permission::firstOrCreate(['name' => "academic_calendar_year.{$action}", 'guard_name' => 'web']);
    }
});

function grantPermission(string $permission, string $schoolId): User
{
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);

    $role = Role::create(['name' => 'academic-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);

    $user->assignRole($role);

    return $user;
}

function yearForSchool(string $schoolId, string $name = '2026-2027'): AcademicCalendarYear
{
    return AcademicCalendarYear::create([
        'school_id' => $schoolId,
        'name' => $name,
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]);
}

test('user with view permission can list calendar years', function (): void {
    $user = grantPermission('academic_calendar_year.view', $this->schoolId);

    expect($user->can('viewAny', AcademicCalendarYear::class))->toBeTrue();
});

test('user without permission cannot list calendar years', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', AcademicCalendarYear::class))->toBeFalse();
});

test('user can view only calendar years of their own school', function (): void {
    $ownSchool = makeTenant();
    $otherSchool = makeTenant();

    $year = yearForSchool($ownSchool);

    $user = grantPermission('academic_calendar_year.view', $ownSchool);
    $outsider = grantPermission('academic_calendar_year.view', $otherSchool);

    expect($user->can('view', $year))->toBeTrue()
        ->and($outsider->can('view', $year))->toBeFalse();
});

test('user needs the create permission to create a year', function (): void {
    $withCreate = grantPermission('academic_calendar_year.create', $this->schoolId);
    $withViewOnly = grantPermission('academic_calendar_year.view', $this->schoolId);

    expect($withCreate->can('create', AcademicCalendarYear::class))->toBeTrue()
        ->and($withViewOnly->can('create', AcademicCalendarYear::class))->toBeFalse();
});

test('user needs the update permission to edit a year', function (): void {
    $year = yearForSchool($this->schoolId);

    $withUpdate = grantPermission('academic_calendar_year.update', $this->schoolId);
    $withViewOnly = grantPermission('academic_calendar_year.view', $this->schoolId);

    expect($withUpdate->can('update', $year))->toBeTrue()
        ->and($withViewOnly->can('update', $year))->toBeFalse();
});

test('user needs the delete permission to delete a year', function (): void {
    $year = yearForSchool($this->schoolId);

    $withDelete = grantPermission('academic_calendar_year.delete', $this->schoolId);
    $withViewOnly = grantPermission('academic_calendar_year.view', $this->schoolId);

    expect($withDelete->can('delete', $year))->toBeTrue()
        ->and($withViewOnly->can('delete', $year))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();

    $year = yearForSchool($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $year))->toBeTrue();
});

test('restore and forceDelete are always denied', function (): void {
    $user = grantPermission('academic_calendar_year.update', $this->schoolId);

    $year = yearForSchool($this->schoolId);

    expect($user->can('restore', $year))->toBeFalse()
        ->and($user->can('forceDelete', $year))->toBeFalse();
});
