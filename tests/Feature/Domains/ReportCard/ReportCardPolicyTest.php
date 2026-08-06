<?php

declare(strict_types=1);

use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'generate', 'regenerate', 'publish', 'delete'] as $action) {
        Permission::firstOrCreate(['name' => "report-card.{$action}", 'guard_name' => 'web']);
    }
});

function reportCardGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'rc-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function reportCardFor(string $schoolId): ReportCard
{
    return ReportCard::factory()->forSchool($schoolId)->create();
}

test('user with the view permission can list report cards', function (): void {
    $user = reportCardGrantPermission('report-card.view', $this->schoolId);

    expect($user->can('viewAny', ReportCard::class))->toBeTrue();
});

test('user without permission cannot list report cards', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', ReportCard::class))->toBeFalse();
});

test('report card access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $reportCard = reportCardFor($this->schoolId);

    $owner = reportCardGrantPermission('report-card.view', $this->schoolId);
    $outsider = reportCardGrantPermission('report-card.view', $otherSchool);

    expect($owner->can('view', $reportCard))->toBeTrue()
        ->and($outsider->can('view', $reportCard))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $reportCard = reportCardFor($this->schoolId);

    $withCreate = reportCardGrantPermission('report-card.create', $this->schoolId);
    $withUpdate = reportCardGrantPermission('report-card.update', $this->schoolId);
    $withGenerate = reportCardGrantPermission('report-card.generate', $this->schoolId);

    expect($withCreate->can('create', ReportCard::class))->toBeTrue()
        ->and($withCreate->can('update', $reportCard))->toBeFalse()
        ->and($withUpdate->can('update', $reportCard))->toBeTrue()
        ->and($withGenerate->can('generate', $reportCard))->toBeTrue()
        ->and($withGenerate->can('publish', $reportCard))->toBeFalse();
});

test('regenerating and publishing need their own capabilities', function (): void {
    $reportCard = reportCardFor($this->schoolId);

    $withRegenerate = reportCardGrantPermission('report-card.regenerate', $this->schoolId);
    $withPublish = reportCardGrantPermission('report-card.publish', $this->schoolId);

    expect($withRegenerate->can('regenerate', $reportCard))->toBeTrue()
        ->and($withRegenerate->can('generate', $reportCard))->toBeFalse()
        ->and($withPublish->can('publish', $reportCard))->toBeTrue()
        ->and($withPublish->can('regenerate', $reportCard))->toBeFalse();
});

test('report card capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $reportCard = reportCardFor($this->schoolId);

    $outsider = reportCardGrantPermission('report-card.publish', $otherSchool);

    expect($outsider->can('publish', $reportCard))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $reportCard = reportCardFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $reportCard))->toBeTrue()
        ->and($superAdmin->can('generate', $reportCard))->toBeTrue()
        ->and($superAdmin->can('regenerate', $reportCard))->toBeTrue()
        ->and($superAdmin->can('publish', $reportCard))->toBeTrue();
});

test('report card runs are never deleted, restored or force deleted', function (): void {
    $reportCard = reportCardFor($this->schoolId);

    $user = reportCardGrantPermission('report-card.delete', $this->schoolId);

    expect($user->can('delete', $reportCard))->toBeFalse()
        ->and($user->can('restore', $reportCard))->toBeFalse()
        ->and($user->can('forceDelete', $reportCard))->toBeFalse();
});
