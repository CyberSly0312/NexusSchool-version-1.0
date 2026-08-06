<?php

declare(strict_types=1);

use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'publish', 'score', 'statistics', 'manage'] as $action) {
        Permission::firstOrCreate(['name' => "assessment.{$action}", 'guard_name' => 'web']);
    }
});

function assessmentGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'assess-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function assessmentFor(string $schoolId): Assessment
{
    return Assessment::factory()
        ->forSchool($schoolId)
        ->create([
            'academic_calendar_year_id' => 1,
            'academic_calendar_term_id' => 1,
            'academic_subject_id' => 1,
            'school_class_id' => 1,
            'staff_id' => 1,
            'assessment_type_id' => 1,
        ]);
}

test('user with the view permission can list assessments', function (): void {
    $user = assessmentGrantPermission('assessment.view', $this->schoolId);

    expect($user->can('viewAny', Assessment::class))->toBeTrue();
});

test('user without permission cannot list assessments', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', Assessment::class))->toBeFalse();
});

test('assessment access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $assessment = assessmentFor($this->schoolId);

    $owner = assessmentGrantPermission('assessment.view', $this->schoolId);
    $outsider = assessmentGrantPermission('assessment.view', $otherSchool);

    expect($owner->can('view', $assessment))->toBeTrue()
        ->and($outsider->can('view', $assessment))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $assessment = assessmentFor($this->schoolId);

    $withCreate = assessmentGrantPermission('assessment.create', $this->schoolId);
    $withUpdate = assessmentGrantPermission('assessment.update', $this->schoolId);
    $withPublish = assessmentGrantPermission('assessment.publish', $this->schoolId);
    $withScore = assessmentGrantPermission('assessment.score', $this->schoolId);

    expect($withCreate->can('create', Assessment::class))->toBeTrue()
        ->and($withCreate->can('update', $assessment))->toBeFalse()
        ->and($withUpdate->can('update', $assessment))->toBeTrue()
        ->and($withPublish->can('publish', $assessment))->toBeTrue()
        ->and($withPublish->can('score', $assessment))->toBeFalse()
        ->and($withScore->can('score', $assessment))->toBeTrue();
});

test('statistics and manage need their own capabilities', function (): void {
    $assessment = assessmentFor($this->schoolId);

    $withStats = assessmentGrantPermission('assessment.statistics', $this->schoolId);
    $withManage = assessmentGrantPermission('assessment.manage', $this->schoolId);

    expect($withStats->can('statistics', $assessment))->toBeTrue()
        ->and($withStats->can('manage', $assessment))->toBeFalse()
        ->and($withManage->can('manage', $assessment))->toBeTrue();
});

test('assessment capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $assessment = assessmentFor($this->schoolId);

    $outsider = assessmentGrantPermission('assessment.score', $otherSchool);

    expect($outsider->can('score', $assessment))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $assessment = assessmentFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $assessment))->toBeTrue()
        ->and($superAdmin->can('publish', $assessment))->toBeTrue()
        ->and($superAdmin->can('statistics', $assessment))->toBeTrue();
});

test('assessment aggregates are never deleted, restored or force deleted', function (): void {
    $assessment = assessmentFor($this->schoolId);
    $type = AssessmentType::factory()->forSchool($this->schoolId)->create();

    $user = assessmentGrantPermission('assessment.manage', $this->schoolId);

    expect($user->can('delete', $assessment))->toBeFalse()
        ->and($user->can('restore', $assessment))->toBeFalse()
        ->and($user->can('forceDelete', $assessment))->toBeFalse()
        ->and($user->can('delete', $type))->toBeFalse()
        ->and($user->can('forceDelete', $type))->toBeFalse();
});
