<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Actions\CreateStaffAction;
use App\Domains\Staff\Application\Actions\StartEmploymentAction;
use App\Domains\Staff\Application\Actions\SuspendStaffAction;
use App\Domains\Staff\Application\DTOs\CreateStaffData;
use App\Domains\Staff\Application\DTOs\EmploymentData;
use App\Domains\Staff\Infrastructure\Models\EmploymentHistory;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\AuditLog;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->actor = User::factory()->create(['school_id' => $this->schoolId]);
});

function createAuditedStaff(object $context): Staff
{
    return app(CreateStaffAction::class)->execute(CreateStaffData::fromArray([
        'school_id' => $context->schoolId,
        'first_name' => 'Adama',
        'last_name' => 'Diallo',
        'gender' => 'male',
        'phone_number' => '+22670112277',
        'email' => 'adama@example.com',
        'specialization' => 'teacher',
    ]), $context->actor);
}

test('registration writes an audit log entry', function (): void {
    $staff = createAuditedStaff($this);

    $log = AuditLog::where('auditable_id', $staff->id)
        ->where('auditable_type', Staff::class)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('created')
        ->and($log->school_id)->toBe($this->schoolId)
        ->and($log->user_id)->toBe($this->actor->id);
});

test('the employment lifecycle is traced immutably in the history timeline', function (): void {
    $staff = createAuditedStaff($this);

    $employment = app(StartEmploymentAction::class)->execute(EmploymentData::fromArray([
        'school_id' => $this->schoolId,
        'staff_id' => $staff->id,
        'contract_start_date' => now()->subYear()->format('Y-m-d'),
    ]), $this->actor);

    app(SuspendStaffAction::class)->execute($staff, 'Enquête', $this->actor);

    $events = EmploymentHistory::where('employment_id', $employment->id)
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($events)->toBe(['employment_started']);
});

test('suspension and restoration are audited', function (): void {
    $staff = createAuditedStaff($this);

    app(SuspendStaffAction::class)->execute($staff, 'Enquête interne', $this->actor);
    app(\App\Domains\Staff\Application\Actions\RestoreStaffAction::class)->execute($staff->fresh(), $this->actor);

    $actions = AuditLog::where('auditable_id', $staff->id)
        ->where('auditable_type', Staff::class)
        ->orderBy('id')
        ->pluck('action')
        ->all();

    expect($actions)->toBe(['created', 'suspended', 'restored']);
});

test('audit logs and history are isolated per school', function (): void {
    $otherSchool = makeTenant();

    $staff = createAuditedStaff($this);
    app(StartEmploymentAction::class)->execute(EmploymentData::fromArray([
        'school_id' => $this->schoolId,
        'staff_id' => $staff->id,
        'contract_start_date' => now()->subYear()->format('Y-m-d'),
    ]), $this->actor);

    expect(AuditLog::forSchool($this->schoolId)->count())->toBeGreaterThan(0)
        ->and(AuditLog::forSchool($otherSchool)->count())->toBe(0)
        ->and(EmploymentHistory::forSchool($this->schoolId)->count())->toBe(1)
        ->and(EmploymentHistory::forSchool($otherSchool)->count())->toBe(0);
});
