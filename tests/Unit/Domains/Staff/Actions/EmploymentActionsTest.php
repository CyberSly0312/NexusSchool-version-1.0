<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Actions\StartEmploymentAction;
use App\Domains\Staff\Application\Actions\TerminateEmploymentAction;
use App\Domains\Staff\Application\Actions\TransferStaffAction;
use App\Domains\Staff\Application\DTOs\EmploymentData;
use App\Domains\Staff\Application\DTOs\TransferData;
use App\Domains\Staff\Domain\Exceptions\EmploymentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->otherSchool = makeTenant();
    $this->staff = Staff::factory()->teacher()->create(['school_id' => $this->schoolId]);
});

function employmentData(string $schoolId, int $staffId, array $overrides = []): EmploymentData
{
    return EmploymentData::fromArray(array_merge([
        'school_id' => $schoolId,
        'staff_id' => $staffId,
        'contract_start_date' => now()->subYear()->format('Y-m-d'),
    ], $overrides));
}

test('an employment can be started for an active staff member', function (): void {
    $employment = app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));

    expect($employment->isActive())->toBeTrue()
        ->and($employment->staff_id)->toBe($this->staff->id);
});

test('a second active employment is rejected', function (): void {
    app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));

    app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));
})->throws(EmploymentException::class);

test('a terminated staff member cannot receive a new contract', function (): void {
    $staff = Staff::factory()->terminated()->create(['school_id' => $this->schoolId]);

    app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $staff->id));
})->throws(InvalidStaffStateException::class);

test('an invalid contract period is rejected', function (): void {
    app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id, [
        'contract_start_date' => '2027-01-01',
        'contract_end_date' => '2026-01-01',
    ]));
})->throws(InvalidArgumentException::class);

test('an employment can be terminated and the staff marked terminated', function (): void {
    $employment = app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));

    app(TerminateEmploymentAction::class)->execute($employment, 'Fin de contrat', now()->format('Y-m-d'));

    expect($employment->fresh()->isTerminated())->toBeTrue()
        ->and($this->staff->fresh()->status->value)->toBe('terminated');
});

test('terminating an inactive employment is rejected', function (): void {
    $employment = Employment::factory()->terminated()->forStaff($this->staff->id)->forSchool($this->schoolId)->create();

    app(TerminateEmploymentAction::class)->execute($employment, 'Fin', now()->format('Y-m-d'));
})->throws(EmploymentException::class);

test('termination before the contract start is rejected', function (): void {
    $employment = app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id, [
        'contract_start_date' => now()->format('Y-m-d'),
    ]));

    app(TerminateEmploymentAction::class)->execute($employment, 'Fin', now()->subDay()->format('Y-m-d'));
})->throws(InvalidArgumentException::class);

test('an active employment can be transferred to another campus', function (): void {
    $employment = app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));

    app(TransferStaffAction::class)->execute(TransferData::fromArray([
        'from_school_id' => $this->schoolId,
        'to_school_id' => $this->otherSchool,
        'staff_id' => $this->staff->id,
    ]));

    expect($employment->fresh()->school_id)->toBe($this->otherSchool);
});

test('a transfer to the same campus is rejected', function (): void {
    app(StartEmploymentAction::class)->execute(employmentData($this->schoolId, $this->staff->id));

    app(TransferStaffAction::class)->execute(TransferData::fromArray([
        'from_school_id' => $this->schoolId,
        'to_school_id' => $this->schoolId,
        'staff_id' => $this->staff->id,
    ]));
})->throws(EmploymentException::class);

test('transferring a staff member without an active employment is rejected', function (): void {
    app(TransferStaffAction::class)->execute(TransferData::fromArray([
        'from_school_id' => $this->schoolId,
        'to_school_id' => $this->otherSchool,
        'staff_id' => $this->staff->id,
    ]));
})->throws(EmploymentException::class);
