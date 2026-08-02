<?php

declare(strict_types=1);

use App\Domains\Student\Application\Services\StudentNumberGenerator;
use App\Domains\Student\Infrastructure\Models\Student;

test('student numbers are sequential per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $generator = app(StudentNumberGenerator::class);

    $first = $generator->generate($schoolA);
    Student::factory()->create(['school_id' => $schoolA, 'student_number' => $first]);

    $second = $generator->generate($schoolA);

    expect($second)->not->toBe($first)
        ->and($generator->generate($schoolB))->not->toBe($first);
});

test('generated numbers keep a school prefix and zero-padded sequence', function (): void {
    $schoolId = makeTenant();
    $generator = app(StudentNumberGenerator::class);

    $number = $generator->generate($schoolId);

    expect($number)->toMatch('/^STU-[\w-]+-\d{4}$/');
});

test('generated numbers do not collide with existing students', function (): void {
    $schoolId = makeTenant();

    Student::factory()->create(['school_id' => $schoolId, 'student_number' => 'STU-TEST-0001']);

    $generator = app(StudentNumberGenerator::class);

    $number = $generator->generate($schoolId);

    expect($number)->not->toBe('STU-TEST-0001')
        ->and(Student::query()->forSchool($schoolId)->where('student_number', $number)->exists())->toBeFalse();
});
