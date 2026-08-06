<?php

declare(strict_types=1);

use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'publish', 'archive', 'manage', 'generate', 'duplicate'] as $action) {
        Permission::firstOrCreate(['name' => "timetable.{$action}", 'guard_name' => 'web']);
    }

    foreach (['view', 'create', 'update', 'delete', 'manage'] as $action) {
        Permission::firstOrCreate(['name' => "schedule.{$action}", 'guard_name' => 'web']);
    }

    Permission::firstOrCreate(['name' => 'conflict.resolve', 'guard_name' => 'web']);
});

function timetableGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'tt-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

test('user with the view permission can list timetables', function (): void {
    $user = timetableGrantPermission('timetable.view', $this->schoolId);

    expect($user->can('viewAny', Timetable::class))->toBeTrue();
});

test('user without permission cannot list timetables', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Timetable::class))->toBeFalse();
});

test('timetable access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $timetable = Timetable::factory()->create(['school_id' => $this->schoolId]);

    $owner = timetableGrantPermission('timetable.view', $this->schoolId);
    $outsider = timetableGrantPermission('timetable.view', $otherSchool);

    expect($owner->can('view', $timetable))->toBeTrue()
        ->and($outsider->can('view', $timetable))->toBeFalse();
});

test('publishing a timetable requires the dedicated permission', function (): void {
    $timetable = Timetable::factory()->create(['school_id' => $this->schoolId]);

    $withPublish = timetableGrantPermission('timetable.publish', $this->schoolId);
    $withViewOnly = timetableGrantPermission('timetable.view', $this->schoolId);

    expect($withPublish->can('publish', $timetable))->toBeTrue()
        ->and($withViewOnly->can('publish', $timetable))->toBeFalse();
});

test('archiving, generating and duplicating need their own permissions', function (): void {
    $timetable = Timetable::factory()->create(['school_id' => $this->schoolId]);

    $withPublish = timetableGrantPermission('timetable.publish', $this->schoolId);
    expect($withPublish->can('archive', $timetable))->toBeFalse()
        ->and($withPublish->can('generate', $timetable))->toBeFalse()
        ->and($withPublish->can('duplicate', $timetable))->toBeFalse();
});

test('schedule entries are guarded by the schedule permission group', function (): void {
    $entry = ScheduleEntry::factory()->create(['school_id' => $this->schoolId]);

    $withSchedule = timetableGrantPermission('schedule.manage', $this->schoolId);
    $withView = timetableGrantPermission('timetable.view', $this->schoolId);

    expect($withSchedule->can('manage', $entry))->toBeTrue()
        ->and($withView->can('manage', $entry))->toBeFalse();
});

test('resolving conflicts requires the conflict resolve permission', function (): void {
    $conflict = TimetableConflict::factory()->create(['school_id' => $this->schoolId]);

    $withResolve = timetableGrantPermission('conflict.resolve', $this->schoolId);
    $withView = timetableGrantPermission('timetable.view', $this->schoolId);

    expect($withResolve->can('resolve', $conflict))->toBeTrue()
        ->and($withView->can('resolve', $conflict))->toBeFalse();
});

test('schedule permissions are school scoped', function (): void {
    $otherSchool = makeTenant();
    $entry = ScheduleEntry::factory()->create(['school_id' => $this->schoolId]);

    $outsider = timetableGrantPermission('schedule.manage', $otherSchool);

    expect($outsider->can('manage', $entry))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $timetable = Timetable::factory()->create(['school_id' => $otherSchool]);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $timetable))->toBeTrue()
        ->and($superAdmin->can('publish', $timetable))->toBeTrue();
});

test('forceDelete is always denied on timetable aggregates', function (): void {
    $user = timetableGrantPermission('timetable.delete', $this->schoolId);
    $timetable = Timetable::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('forceDelete', $timetable))->toBeFalse();
});
