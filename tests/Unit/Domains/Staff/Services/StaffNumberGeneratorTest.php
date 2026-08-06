<?php

declare(strict_types=1);

use App\Domains\Staff\Application\Services\StaffNumberGenerator;
use App\Domains\Staff\Infrastructure\Models\Staff;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->generator = new StaffNumberGenerator();
});

test('generated numbers follow the STF-PREFIX-SEQUENCE format', function (): void {
    $number = $this->generator->generate($this->schoolId);

    expect($number)->toMatch('/^STF-[0-9A-F]{6}-0001$/');
});

test('sequences increment per school', function (): void {
    Staff::factory()->create([
        'school_id' => $this->schoolId,
        'staff_number' => $this->generator->generate($this->schoolId),
    ]);

    expect($this->generator->generate($this->schoolId))->toMatch('/^STF-[0-9A-F]{6}-0002$/');
});

test('the sequence is isolated between schools', function (): void {
    $otherSchool = makeTenant();

    Staff::factory()->create([
        'school_id' => $this->schoolId,
        'staff_number' => $this->generator->generate($this->schoolId),
    ]);

    expect($this->generator->generate($this->schoolId))->toMatch('/-0002$/')
        ->and($this->generator->generate($otherSchool))->toMatch('/-0001$/');
});

test('format renders a zero-padded sequence', function (): void {
    expect($this->generator->format($this->schoolId, 42))->toMatch('/^STF-[0-9A-F]{6}-0042$/');
});
