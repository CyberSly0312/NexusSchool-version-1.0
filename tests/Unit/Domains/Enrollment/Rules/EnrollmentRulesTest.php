<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Rules\ActiveAcademicYearRule;
use App\Domains\Enrollment\Domain\Rules\UniqueActiveEnrollmentRule;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Student\Infrastructure\Models\Student;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class] = makeEnrollmentContext($this->schoolId);
});

test('the active academic year rule passes for an active year', function (): void {
    expect((new ActiveAcademicYearRule($this->year->id))->fails())->toBeFalse();
});

test('the active academic year rule fails for a draft year', function (): void {
    $draft = AcademicCalendarYear::create([
        'school_id' => $this->schoolId,
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'draft',
    ]);

    expect((new ActiveAcademicYearRule($draft->id))->fails())->toBeTrue();
});

test('the active academic year rule fails for a missing year', function (): void {
    expect((new ActiveAcademicYearRule(999999))->fails())->toBeTrue();
});

test('the unique active enrollment rule fails when an active row exists', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    Enrollment::factory()->create([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'status' => EnrollmentStatus::Active,
    ]);

    expect((new UniqueActiveEnrollmentRule($student->id, $this->year->id))->fails())->toBeTrue();
});

test('the unique active enrollment rule passes for closed or archived rows', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    Enrollment::factory()->create([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'status' => EnrollmentStatus::Withdrawn,
    ]);
    Enrollment::factory()->create([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'status' => EnrollmentStatus::Graduated,
    ]);

    expect((new UniqueActiveEnrollmentRule($student->id, $this->year->id))->fails())->toBeFalse();
});

test('the unique active enrollment rule ignores the given enrollment id', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    $enrollment = Enrollment::factory()->create([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'status' => EnrollmentStatus::Active,
    ]);

    expect((new UniqueActiveEnrollmentRule($student->id, $this->year->id, $enrollment->id))->fails())->toBeFalse();
});

test('the rules fail the field through the validator contract', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    Enrollment::factory()->create([
        'school_id' => $this->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $this->year->id,
        'status' => EnrollmentStatus::Active,
    ]);

    $failures = [];
    $rule = new UniqueActiveEnrollmentRule($student->id, $this->year->id);

    $rule->validate('academic_calendar_year_id', $this->year->id, function (string $message) use (&$failures): void {
        $failures[] = $message;
    });

    expect($failures)->toHaveCount(1);

    $yearFailures = [];
    $yearRule = new ActiveAcademicYearRule(999999);

    $yearRule->validate('academic_calendar_year_id', 999999, function (string $message) use (&$yearFailures): void {
        $yearFailures[] = $message;
    });

    expect($yearFailures)->toHaveCount(1);
});
