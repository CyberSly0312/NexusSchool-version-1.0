<?php

declare(strict_types=1);

use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'record', 'correct', 'lock', 'unlock', 'report', 'manage'] as $action) {
        Permission::firstOrCreate(['name' => "attendance.{$action}", 'guard_name' => 'web']);
    }
});

function attendanceGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'att-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function attendanceSessionFor(string $schoolId): AttendanceSession
{
    return AttendanceSession::create([
        'school_id' => $schoolId,
        'school_class_id' => 1,
        'date' => '2026-09-07',
        'day_of_week' => 0,
        'period_number' => 1,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
        'status' => 'open',
    ]);
}

test('user with the view permission can list attendance sessions', function (): void {
    $user = attendanceGrantPermission('attendance.view', $this->schoolId);

    expect($user->can('viewAny', AttendanceSession::class))->toBeTrue();
});

test('user without permission cannot list attendance sessions', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', AttendanceSession::class))->toBeFalse();
});

test('session access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $session = attendanceSessionFor($this->schoolId);

    $owner = attendanceGrantPermission('attendance.view', $this->schoolId);
    $outsider = attendanceGrantPermission('attendance.view', $otherSchool);

    expect($owner->can('view', $session))->toBeTrue()
        ->and($outsider->can('view', $session))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $session = attendanceSessionFor($this->schoolId);

    $withRecord = attendanceGrantPermission('attendance.record', $this->schoolId);
    $withCorrect = attendanceGrantPermission('attendance.correct', $this->schoolId);
    $withLock = attendanceGrantPermission('attendance.lock', $this->schoolId);

    expect($withRecord->can('record', $session))->toBeTrue()
        ->and($withRecord->can('correct', $session))->toBeFalse()
        ->and($withCorrect->can('correct', $session))->toBeTrue()
        ->and($withLock->can('lock', $session))->toBeTrue()
        ->and($withLock->can('unlock', $session))->toBeFalse();
});

test('unlock and report need their own capabilities', function (): void {
    $session = attendanceSessionFor($this->schoolId);

    $withUnlock = attendanceGrantPermission('attendance.unlock', $this->schoolId);
    $withReport = attendanceGrantPermission('attendance.report', $this->schoolId);

    expect($withUnlock->can('unlock', $session))->toBeTrue()
        ->and($withUnlock->can('report', $session))->toBeFalse()
        ->and($withReport->can('report', $session))->toBeTrue();
});

test('attendance capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $session = attendanceSessionFor($this->schoolId);

    $outsider = attendanceGrantPermission('attendance.record', $otherSchool);

    expect($outsider->can('record', $session))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $session = attendanceSessionFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $session))->toBeTrue()
        ->and($superAdmin->can('record', $session))->toBeTrue()
        ->and($superAdmin->can('unlock', $session))->toBeTrue();
});

test('attendance aggregates are never force deleted', function (): void {
    $session = attendanceSessionFor($this->schoolId);

    $user = attendanceGrantPermission('attendance.manage', $this->schoolId);

    expect($user->can('forceDelete', $session))->toBeFalse()
        ->and($user->can('delete', $session))->toBeFalse();
});
