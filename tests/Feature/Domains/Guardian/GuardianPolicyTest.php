<?php

declare(strict_types=1);

use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'delete', 'link', 'unlink', 'primary', 'emergency', 'pickup'] as $action) {
        Permission::firstOrCreate(['name' => "guardian.{$action}", 'guard_name' => 'web']);
    }
});

function guardianGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'guardian-manager-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

test('user with the view permission can list guardians', function (): void {
    $user = guardianGrantPermission('guardian.view', $this->schoolId);

    expect($user->can('viewAny', Guardian::class))->toBeTrue();
});

test('user without permission cannot list guardians', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Guardian::class))->toBeFalse();
});

test('user can view only guardians of their own school', function (): void {
    $ownSchool = makeTenant();
    $otherSchool = makeTenant();

    $guardian = Guardian::factory()->create(['school_id' => $ownSchool]);

    $owner = guardianGrantPermission('guardian.view', $ownSchool);
    $outsider = guardianGrantPermission('guardian.view', $otherSchool);

    expect($owner->can('view', $guardian))->toBeTrue()
        ->and($outsider->can('view', $guardian))->toBeFalse();
});

test('user needs the create permission to register a guardian', function (): void {
    $withCreate = guardianGrantPermission('guardian.create', $this->schoolId);
    $withViewOnly = guardianGrantPermission('guardian.view', $this->schoolId);

    expect($withCreate->can('create', Guardian::class))->toBeTrue()
        ->and($withViewOnly->can('create', Guardian::class))->toBeFalse();
});

test('relationship actions require their dedicated permission', function (): void {
    $relationship = StudentGuardianRelationship::factory()->create(['school_id' => $this->schoolId]);

    $withLink = guardianGrantPermission('guardian.link', $this->schoolId);
    $withViewOnly = guardianGrantPermission('guardian.view', $this->schoolId);

    expect($withLink->can('link', Guardian::class))->toBeTrue()
        ->and($withViewOnly->can('link', Guardian::class))->toBeFalse()
        ->and($withLink->can('unlink', $relationship))->toBeFalse();
});

test('unlink requires its dedicated permission on the relationship', function (): void {
    $relationship = StudentGuardianRelationship::factory()->create(['school_id' => $this->schoolId]);

    $withUnlink = guardianGrantPermission('guardian.unlink', $this->schoolId);

    expect($withUnlink->can('unlink', $relationship))->toBeTrue();
});

test('primary assignment requires the primary permission', function (): void {
    $relationship = StudentGuardianRelationship::factory()->create(['school_id' => $this->schoolId]);

    $withPrimary = guardianGrantPermission('guardian.primary', $this->schoolId);
    $withViewOnly = guardianGrantPermission('guardian.view', $this->schoolId);

    expect($withPrimary->can('assignPrimary', $relationship))->toBeTrue()
        ->and($withViewOnly->can('assignPrimary', $relationship))->toBeFalse();
});

test('relationship permissions are school scoped', function (): void {
    $otherSchool = makeTenant();

    $relationship = StudentGuardianRelationship::factory()->create(['school_id' => $this->schoolId]);
    $outsider = guardianGrantPermission('guardian.emergency', $otherSchool);

    expect($outsider->can('assignEmergency', $relationship))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();

    $guardian = Guardian::factory()->create(['school_id' => $otherSchool]);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $guardian))->toBeTrue()
        ->and($superAdmin->can('assignPickup', StudentGuardianRelationship::factory()->create(['school_id' => $otherSchool])))->toBeTrue();
});

test('forceDelete is always denied', function (): void {
    $user = guardianGrantPermission('guardian.delete', $this->schoolId);
    $guardian = Guardian::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('forceDelete', $guardian))->toBeFalse();
});
