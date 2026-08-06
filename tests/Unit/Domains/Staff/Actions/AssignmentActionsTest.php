<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Domain\Exceptions\AssignmentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Position;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\Department;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->otherSchool = makeTenant();
    $this->service = app(StaffService::class);
    $this->staff = Staff::factory()->teacher()->create(['school_id' => $this->schoolId]);
    $this->department = Department::create(['school_id' => $this->schoolId, 'name' => 'Éducation', 'code' => 'EDU', 'status' => 'active']);
    $this->position = Position::factory()->teaching()->forSchool($this->schoolId)->create();
});

test('a department assignment is created and current', function (): void {
    $assignment = $this->service->assignDepartment($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'department',
        'department_id' => $this->department->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);

    expect($assignment->isCurrent())->toBeTrue()
        ->and($assignment->assignment_type->value)->toBe('department');
});

test('assigning a department from another school is rejected', function (): void {
    $foreignDepartment = Department::create(['school_id' => $this->otherSchool, 'name' => 'Étranger', 'code' => 'EXT', 'status' => 'active']);

    $this->service->assignDepartment($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'department',
        'department_id' => $foreignDepartment->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);
})->throws(AssignmentException::class);

test('overlapping department assignments are rejected', function (): void {
    $this->service->assignDepartment($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'department',
        'department_id' => $this->department->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);

    $this->service->assignDepartment($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'department',
        'department_id' => $this->department->id,
        'assignment_start_date' => now()->addDay()->format('Y-m-d'),
    ]);
})->throws(AssignmentException::class);

test('a position assignment is created and current', function (): void {
    $assignment = $this->service->assignPosition($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'position',
        'position_id' => $this->position->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);

    expect($assignment->isCurrent())->toBeTrue()
        ->and($assignment->assignment_type->value)->toBe('position');
});

test('assigning a position from another school is rejected', function (): void {
    $foreignPosition = Position::factory()->forSchool($this->otherSchool)->create();

    $this->service->assignPosition($this->schoolId, [
        'staff_id' => $this->staff->id,
        'assignment_type' => 'position',
        'position_id' => $foreignPosition->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);
})->throws(AssignmentException::class);

test('assignments are rejected for an immutable staff member', function (): void {
    $staff = Staff::factory()->terminated()->create(['school_id' => $this->schoolId]);

    $this->service->assignDepartment($this->schoolId, [
        'staff_id' => $staff->id,
        'assignment_type' => 'department',
        'department_id' => $this->department->id,
        'assignment_start_date' => now()->format('Y-m-d'),
    ]);
})->throws(InvalidStaffStateException::class);
