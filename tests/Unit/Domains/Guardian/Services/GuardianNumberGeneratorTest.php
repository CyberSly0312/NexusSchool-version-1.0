<?php

declare(strict_types=1);

use App\Domains\Guardian\Application\Services\GuardianNumberGenerator;
use App\Domains\Guardian\Infrastructure\Models\Guardian;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->generator = new GuardianNumberGenerator();
});

test('generated numbers follow the GUA-PREFIX-SEQUENCE format', function (): void {
    $number = $this->generator->generate($this->schoolId);

    expect($number)->toMatch('/^GUA-[0-9A-F]{6}-0001$/');
});

test('sequences increment per school', function (): void {
    Guardian::factory()->create([
        'school_id' => $this->schoolId,
        'guardian_number' => $this->generator->generate($this->schoolId),
    ]);

    expect($this->generator->generate($this->schoolId))->toMatch('/^GUA-[0-9A-F]{6}-0002$/');
});

test('the sequence is isolated between schools', function (): void {
    $otherSchool = makeTenant();

    Guardian::factory()->create([
        'school_id' => $this->schoolId,
        'guardian_number' => $this->generator->generate($this->schoolId),
    ]);

    expect($this->generator->generate($this->schoolId))->toMatch('/-0002$/')
        ->and($this->generator->generate($otherSchool))->toMatch('/-0001$/');
});

test('format renders a zero-padded sequence', function (): void {
    expect($this->generator->format($this->schoolId, 42))->toMatch('/^GUA-[0-9A-F]{6}-0042$/');
});
