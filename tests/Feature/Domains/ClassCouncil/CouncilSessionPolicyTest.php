<?php

declare(strict_types=1);

use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'convene', 'decide', 'publish', 'archive', 'delete'] as $action) {
        Permission::firstOrCreate(['name' => "class-council.{$action}", 'guard_name' => 'web']);
    }
});

function classCouncilGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'cc-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function classCouncilSessionFor(string $schoolId): CouncilSession
{
    return CouncilSession::factory()->forSchool($schoolId)->create();
}

test('user with the view permission can list council sessions', function (): void {
    $user = classCouncilGrantPermission('class-council.view', $this->schoolId);

    expect($user->can('viewAny', CouncilSession::class))->toBeTrue();
});

test('user without permission cannot list council sessions', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', CouncilSession::class))->toBeFalse();
});

test('council session access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $session = classCouncilSessionFor($this->schoolId);

    $owner = classCouncilGrantPermission('class-council.view', $this->schoolId);
    $outsider = classCouncilGrantPermission('class-council.view', $otherSchool);

    expect($owner->can('view', $session))->toBeTrue()
        ->and($outsider->can('view', $session))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $session = classCouncilSessionFor($this->schoolId);

    $withCreate = classCouncilGrantPermission('class-council.create', $this->schoolId);
    $withUpdate = classCouncilGrantPermission('class-council.update', $this->schoolId);
    $withConvene = classCouncilGrantPermission('class-council.convene', $this->schoolId);

    expect($withCreate->can('create', CouncilSession::class))->toBeTrue()
        ->and($withCreate->can('update', $session))->toBeFalse()
        ->and($withUpdate->can('update', $session))->toBeTrue()
        ->and($withConvene->can('convene', $session))->toBeTrue()
        ->and($withConvene->can('decide', $session))->toBeFalse();
});

test('deciding, publishing and archiving need their own capabilities', function (): void {
    $session = classCouncilSessionFor($this->schoolId);

    $withDecide = classCouncilGrantPermission('class-council.decide', $this->schoolId);
    $withPublish = classCouncilGrantPermission('class-council.publish', $this->schoolId);
    $withArchive = classCouncilGrantPermission('class-council.archive', $this->schoolId);

    expect($withDecide->can('decide', $session))->toBeTrue()
        ->and($withDecide->can('publish', $session))->toBeFalse()
        ->and($withPublish->can('publish', $session))->toBeTrue()
        ->and($withPublish->can('archive', $session))->toBeFalse()
        ->and($withArchive->can('archive', $session))->toBeTrue()
        ->and($withArchive->can('decide', $session))->toBeFalse();
});

test('council lifecycle capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $session = classCouncilSessionFor($this->schoolId);

    $outsider = classCouncilGrantPermission('class-council.publish', $otherSchool);

    expect($outsider->can('publish', $session))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $session = classCouncilSessionFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $session))->toBeTrue()
        ->and($superAdmin->can('convene', $session))->toBeTrue()
        ->and($superAdmin->can('decide', $session))->toBeTrue()
        ->and($superAdmin->can('publish', $session))->toBeTrue()
        ->and($superAdmin->can('archive', $session))->toBeTrue();
});

test('council sessions are never deleted, restored or force deleted', function (): void {
    $session = classCouncilSessionFor($this->schoolId);

    $user = classCouncilGrantPermission('class-council.delete', $this->schoolId);

    expect($user->can('delete', $session))->toBeFalse()
        ->and($user->can('restore', $session))->toBeFalse()
        ->and($user->can('forceDelete', $session))->toBeFalse();
});
