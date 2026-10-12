<?php

declare(strict_types=1);

use App\Domains\RecordsLifecycle\Application\DTOs\RecordLifecycleData;
use App\Domains\RecordsLifecycle\Application\Services\RecordLifecycleService;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantRecordLifecyclePermissions(User $user, array $actions): void
{
    foreach ($actions as $action) {
        $user->givePermissionTo(Permission::findOrCreate('records_lifecycle.'.$action, 'web'));
    }
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->service = app(RecordLifecycleService::class);
    $this->actor = User::factory()->create(['school_id' => $this->schoolId]);
    grantRecordLifecyclePermissions($this->actor, ['create', 'view', 'publish', 'archive']);
});

test('it registers a school-owned lifecycle envelope and publishes it', function (): void {
    $record = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $this->schoolId,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 101,
        'source_version' => 'v1',
        'lifecycle_state' => RecordLifecycleState::Registered->value,
        'academic_context' => ['year_id' => 1, 'term_id' => 1],
        'origin_event' => 'ReportCardGenerated',
    ]), $this->actor);

    expect($record->school_id)->toBe($this->schoolId)
        ->and($record->lifecycle_state)->toBe(RecordLifecycleState::Registered)
        ->and($record->uuid)->not->toBeNull();

    $published = $this->service->publish($record->id, $this->actor);

    expect($published->lifecycle_state)->toBe(RecordLifecycleState::Published)
        ->and($published->published_at)->not->toBeNull();
});

test('it archives a record once it is no longer active', function (): void {
    $record = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $this->schoolId,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 102,
        'lifecycle_state' => RecordLifecycleState::Registered->value,
        'origin_event' => 'ReportCardGenerated',
    ]), $this->actor);

    $archived = $this->service->archive($record->id, $this->actor);

    expect($archived->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($archived->archived_at)->not->toBeNull();
});

test('it archives a published record after publication', function (): void {
    $record = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $this->schoolId,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 108,
        'lifecycle_state' => RecordLifecycleState::Registered->value,
        'origin_event' => 'ReportCardGenerated',
    ]), $this->actor);

    $this->service->publish($record->id, $this->actor);

    $archived = $this->service->archive($record->id, $this->actor);

    expect($archived->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($archived->archived_at)->not->toBeNull();
});

test('it keeps the envelope scoped to its school', function (): void {
    $otherSchool = makeTenant('Other School');
    $otherActor = User::factory()->create(['school_id' => $otherSchool]);
    grantRecordLifecyclePermissions($otherActor, ['create', 'view', 'publish', 'archive']);
    $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $this->schoolId,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 103,
        'lifecycle_state' => RecordLifecycleState::Registered->value,
        'origin_event' => 'ReportCardGenerated',
    ]), $this->actor);

    $other = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $otherSchool,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 104,
        'lifecycle_state' => RecordLifecycleState::Registered->value,
        'origin_event' => 'ReportCardGenerated',
    ]), $otherActor);

    expect(RecordLifecycle::query()->forSchool($this->schoolId)->count())->toBe(1)
        ->and(RecordLifecycle::query()->forSchool($otherSchool)->count())->toBe(1)
        ->and($other->school_id)->toBe($otherSchool)
        ->and($this->actor->can('view', $other))->toBeFalse()
        ->and($otherActor->can('view', $other))->toBeTrue();
});

test('it denies cross-school registration and lifecycle transitions', function (): void {
    $otherSchool = makeTenant('Other School');
    $otherActor = User::factory()->create(['school_id' => $otherSchool]);
    grantRecordLifecyclePermissions($otherActor, ['create', 'view', 'publish', 'archive']);

    expect(fn () => $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $otherSchool,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 105,
    ]), $this->actor))->toThrow(AuthorizationException::class);

    $record = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $otherSchool,
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 106,
    ]), $otherActor);

    expect(fn () => $this->service->publish($record->id, $this->actor))->toThrow(AuthorizationException::class)
        ->and(fn () => $this->service->archive($record->id, $this->actor))->toThrow(AuthorizationException::class);
});

test('it authorizes the super admin bypass and rejects invalid transitions', function (): void {
    $superAdmin = User::factory()->create(['school_id' => makeTenant('Platform School')]);
    $superAdmin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $record = $this->service->register(RecordLifecycleData::fromArray([
        'school_id' => $this->schoolId,
        'source_domain' => 'academic_communication',
        'source_type' => 'academic_document',
        'source_id' => 107,
    ]), $this->actor);

    $published = $this->service->publish($record->id, $superAdmin);

    expect($published->lifecycle_state)->toBe(RecordLifecycleState::Published)
        ->and(fn () => $this->service->publish($record->id, $superAdmin))->toThrow(InvalidArgumentException::class);
});
