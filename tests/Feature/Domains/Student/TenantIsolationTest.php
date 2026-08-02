<?php

declare(strict_types=1);

use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Infrastructure\Models\StudentGuardian;

test('students are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Student::factory()->create(['school_id' => $schoolA]);
    Student::factory()->create(['school_id' => $schoolA]);
    Student::factory()->create(['school_id' => $schoolB]);

    expect(Student::forSchool($schoolA)->count())->toBe(2)
        ->and(Student::forSchool($schoolB)->count())->toBe(1);
});

test('the same student number is allowed across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Student::factory()->create(['school_id' => $schoolA, 'student_number' => 'STU-SAME-1']);
    Student::factory()->create(['school_id' => $schoolB, 'student_number' => 'STU-SAME-1']);

    expect(Student::forSchool($schoolA)->where('student_number', 'STU-SAME-1')->exists())->toBeTrue()
        ->and(Student::forSchool($schoolB)->where('student_number', 'STU-SAME-1')->exists())->toBeTrue();
});

test('the database rejects a duplicate student number within one school', function (): void {
    $school = makeTenant();

    Student::factory()->create(['school_id' => $school, 'student_number' => 'STU-DUP-1']);

    Student::factory()->create(['school_id' => $school, 'student_number' => 'STU-DUP-1']);
})->throws(Illuminate\Database\QueryException::class);

test('guardian pivots are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $studentA = Student::factory()->create(['school_id' => $schoolA]);
    $studentB = Student::factory()->create(['school_id' => $schoolB]);

    StudentGuardian::create([
        'school_id' => $schoolA,
        'student_id' => $studentA->id,
        'name' => 'Mme Aya',
        'relationship' => 'parent',
        'is_primary' => true,
    ]);

    expect(StudentGuardian::forSchool($schoolA)->count())->toBe(1)
        ->and(StudentGuardian::forSchool($schoolB)->count())->toBe(0)
        ->and($studentA->guardians()->count())->toBe(1)
        ->and($studentB->guardians()->count())->toBe(0);
});

test('archived students are excluded from the default scope', function (): void {
    $school = makeTenant();

    Student::factory()->archived()->create(['school_id' => $school]);
    Student::factory()->create(['school_id' => $school]);

    expect(Student::count())->toBe(1)
        ->and(Student::withTrashed()->count())->toBe(2);
});
