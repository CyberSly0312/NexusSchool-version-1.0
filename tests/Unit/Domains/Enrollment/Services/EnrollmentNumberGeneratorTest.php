<?php

declare(strict_types=1);

use App\Domains\Enrollment\Application\Services\EnrollmentNumberGenerator;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

test('generates a sequential school-scoped number', function (): void {
    $schoolId = makeTenant();
    $generator = app(EnrollmentNumberGenerator::class);

    $first = $generator->generate($schoolId);

    expect($first)->toMatch('/^ENR-[A-F0-9]{6}-0001$/');

    Enrollment::factory()->create(['school_id' => $schoolId, 'enrollment_number' => $first]);

    expect($generator->generate($schoolId))->toMatch('/^ENR-[A-F0-9]{6}-0002$/');
});

test('the sequence restarts for each school', function (): void {
    $generator = app(EnrollmentNumberGenerator::class);

    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $firstA = $generator->generate($schoolA);

    expect($firstA)->toMatch('/-0001$/')
        ->and($generator->generate($schoolB))->toMatch('/-0001$/');

    Enrollment::factory()->create(['school_id' => $schoolA, 'enrollment_number' => $firstA]);

    expect($generator->generate($schoolA))->toMatch('/-0002$/')
        ->and($generator->generate($schoolB))->toMatch('/-0001$/');
});

test('format embeds the school prefix', function (): void {
    $generator = app(EnrollmentNumberGenerator::class);

    $a = $generator->format('school-a', 1);
    $b = $generator->format('school-b', 1);

    expect(substr($a, 4, 6))->toBe(strtoupper(substr(md5('school-a'), 0, 6)))
        ->and(substr($b, 4, 6))->toBe(strtoupper(substr(md5('school-b'), 0, 6)))
        ->and($a)->not->toBe($b);
});

test('generated numbers satisfy the enrollment number value object', function (): void {
    $generator = app(EnrollmentNumberGenerator::class);

    $number = $generator->generate(makeTenant());

    expect(new \App\Domains\Enrollment\Domain\ValueObjects\EnrollmentNumber($number))
        ->toBeInstanceOf(\App\Domains\Enrollment\Domain\ValueObjects\EnrollmentNumber::class);
});
