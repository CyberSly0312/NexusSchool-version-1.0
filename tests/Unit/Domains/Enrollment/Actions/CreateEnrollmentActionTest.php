<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Events\EnrollmentCreated;
use App\Domains\Enrollment\Domain\Exceptions\DuplicateEnrollmentException;
use App\Domains\Enrollment\Domain\Exceptions\InactiveAcademicYearException;
use App\Domains\Enrollment\Domain\Exceptions\InvalidEnrollmentDateException;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class, $this->program, $this->level] = makeEnrollmentContext($this->schoolId);
    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);
});

function enrollmentData(
    string $schoolId,
    int $studentId,
    int $yearId,
    int $programId,
    int $levelId,
    int $classId,
    array $overrides = [],
): CreateEnrollmentData {
    return CreateEnrollmentData::fromArray(array_merge([
        'school_id' => $schoolId,
        'student_id' => $studentId,
        'academic_calendar_year_id' => $yearId,
        'academic_program_id' => $programId,
        'education_level_id' => $levelId,
        'school_class_id' => $classId,
        'enrolled_at' => '2026-09-15',
    ], $overrides));
}

function baseEnrollmentData(object $context, object $student, array $overrides = []): CreateEnrollmentData
{
    return enrollmentData(
        $context->schoolId,
        $student->id,
        $context->year->id,
        $context->program->id,
        $context->level->id,
        $context->class->id,
        $overrides,
    );
}

test('it creates an enrollment and generates an enrollment number', function (): void {
    Event::fake([EnrollmentCreated::class]);

    $enrollment = app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student),
    );

    expect($enrollment->exists)->toBeTrue()
        ->and($enrollment->enrollment_number)->toMatch('/^ENR-[A-F0-9]{6}-[0-9]{4}$/')
        ->and($enrollment->school_id)->toBe($this->schoolId)
        ->and($enrollment->student_id)->toBe($this->student->id)
        ->and($enrollment->status)->toBe(EnrollmentStatus::Active)
        ->and($enrollment->enrollment_type)->toBe(EnrollmentType::New);

    Event::assertDispatched(EnrollmentCreated::class);
});

test('it keeps an explicit enrollment number', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrollment_number' => 'ENR-ABCDEF-0001']),
    );

    expect($enrollment->enrollment_number)->toBe('ENR-ABCDEF-0001');
});

test('it rejects a duplicate enrollment number in the same school', function (): void {
    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrollment_number' => 'ENR-DUP-1']),
    );

    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrollment_number' => 'ENR-DUP-1']),
    );
})->throws(DuplicateEnrollmentException::class);

test('it allows the same enrollment number across schools', function (): void {
    $otherSchool = makeTenant();
    [$otherYear, $otherClass, $otherProgram, $otherLevel] = makeEnrollmentContext($otherSchool);
    $otherStudent = Student::factory()->create(['school_id' => $otherSchool]);

    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrollment_number' => 'ENR-SHARED-1']),
    );

    $enrollment = app(CreateEnrollmentAction::class)->execute(
        enrollmentData($otherSchool, $otherStudent->id, $otherYear->id, $otherProgram->id, $otherLevel->id, $otherClass->id, [
            'enrollment_number' => 'ENR-SHARED-1',
        ]),
    );

    expect($enrollment->school_id)->toBe($otherSchool);
});

test('it rejects a second active enrollment for the same year', function (): void {
    app(CreateEnrollmentAction::class)->execute(baseEnrollmentData($this, $this->student));

    app(CreateEnrollmentAction::class)->execute(baseEnrollmentData($this, $this->student));
})->throws(DuplicateEnrollmentException::class);

test('it allows a new enrollment in another academic year', function (): void {
    app(CreateEnrollmentAction::class)->execute(baseEnrollmentData($this, $this->student));

    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $enrollment = app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, [
            'academic_calendar_year_id' => $nextYear->id,
            'enrolled_at' => '2027-09-15',
        ]),
    );

    expect($enrollment->academic_calendar_year_id)->toBe($nextYear->id);
});

test('it rejects an inactive academic year', function (): void {
    $inactiveYear = AcademicCalendarYear::create([
        'school_id' => $this->schoolId,
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'draft',
    ]);

    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['academic_calendar_year_id' => $inactiveYear->id]),
    );
})->throws(InactiveAcademicYearException::class);

test('it rejects a student from another school', function (): void {
    $otherSchool = makeTenant();
    $otherStudent = Student::factory()->create(['school_id' => $otherSchool]);

    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $otherStudent),
    );
})->throws(InvalidArgumentException::class);

test('it rejects a class from another school', function (): void {
    $otherSchool = makeTenant();
    [, , $otherProgram, $otherLevel] = makeEnrollmentContext($otherSchool);
    $otherClass = SchoolClass::create([
        'school_id' => $otherSchool,
        'academic_program_id' => $otherProgram->id,
        'education_level_id' => $otherLevel->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    app(CreateEnrollmentAction::class)->execute(
        enrollmentData($this->schoolId, $this->student->id, $this->year->id, $this->program->id, $this->level->id, $otherClass->id),
    );
})->throws(InvalidArgumentException::class);

test('it rejects a program from another school', function (): void {
    $otherSchool = makeTenant();
    [$otherYear, $otherClass, , $otherLevel] = makeEnrollmentContext($otherSchool);

    app(CreateEnrollmentAction::class)->execute(
        enrollmentData($this->schoolId, $this->student->id, $this->year->id, $otherClass->academic_program_id, $otherLevel->id, $this->class->id),
    );
})->throws(InvalidArgumentException::class);

test('it rejects an education level from another school', function (): void {
    $otherSchool = makeTenant();
    [$otherYear, $otherClass, $otherProgram] = makeEnrollmentContext($otherSchool);

    app(CreateEnrollmentAction::class)->execute(
        enrollmentData($this->schoolId, $this->student->id, $this->year->id, $this->program->id, $otherClass->education_level_id, $this->class->id),
    );
})->throws(InvalidArgumentException::class);

test('it rejects a section that does not belong to the class', function (): void {
    $otherSchool = makeTenant();
    [, $otherClass, $otherProgram, $otherLevel] = makeEnrollmentContext($otherSchool);
    $foreignSection = ClassSection::create([
        'school_id' => $this->schoolId,
        'school_class_id' => SchoolClass::create([
            'school_id' => $this->schoolId,
            'academic_program_id' => $otherProgram->id,
            'education_level_id' => $otherLevel->id,
            'name' => '3ème B',
            'code' => 'CL3B',
            'status' => 'active',
        ])->id,
        'name' => 'B',
        'code' => 'SEC-B',
        'status' => 'active',
    ]);

    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['class_section_id' => $foreignSection->id]),
    );
})->throws(InvalidArgumentException::class);

test('it rejects an enrollment date before the academic year starts', function (): void {
    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrolled_at' => '2026-08-31']),
    );
})->throws(InvalidEnrollmentDateException::class);

test('it rejects an enrollment date after the academic year ends', function (): void {
    app(CreateEnrollmentAction::class)->execute(
        baseEnrollmentData($this, $this->student, ['enrolled_at' => '2027-08-01']),
    );
})->throws(InvalidEnrollmentDateException::class);

test('it writes an audit log and a history entry on create', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(baseEnrollmentData($this, $this->student));

    expect(AuditLog::where('auditable_id', $enrollment->id)->where('action', 'created')->exists())->toBeTrue()
        ->and(EnrollmentHistory::where('enrollment_id', $enrollment->id)->where('event', 'created')->exists())->toBeTrue();
});
