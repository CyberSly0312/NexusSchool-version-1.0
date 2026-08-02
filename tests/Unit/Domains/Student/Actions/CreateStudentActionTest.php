<?php

declare(strict_types=1);

use App\Domains\Student\Application\Actions\CreateStudentAction;
use App\Domains\Student\Application\DTOs\CreateStudentData;
use App\Domains\Student\Domain\Events\StudentCreated;
use App\Domains\Student\Domain\Exceptions\AcademicAssignmentException;
use App\Domains\Student\Domain\Exceptions\DuplicateStudentException;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class] = makeAcademicContext($this->schoolId);
});

function createStudentData(string $schoolId, int $yearId, int $classId, array $overrides = []): CreateStudentData
{
    return CreateStudentData::fromArray(array_merge([
        'school_id' => $schoolId,
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'gender' => 'male',
        'date_of_birth' => '2015-05-10',
        'nationality' => 'Ivoirienne',
        'admission_date' => '2026-09-01',
        'academic_calendar_year_id' => $yearId,
        'school_class_id' => $classId,
        'status' => 'active',
    ], $overrides));
}

test('it creates a student and generates a student number', function (): void {
    Event::fake([StudentCreated::class]);

    $student = app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id),
    );

    expect($student->exists)->toBeTrue()
        ->and($student->student_number)->toMatch('/^STU-/')
        ->and($student->school_id)->toBe($this->schoolId)
        ->and($student->full_name)->toBe('Jean Dupont');

    Event::assertDispatched(StudentCreated::class);
});

test('it keeps an explicit student number', function (): void {
    $student = app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['student_number' => 'STU-X-0001']),
    );

    expect($student->student_number)->toBe('STU-X-0001');
});

test('it rejects a duplicate student number in the same school', function (): void {
    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['student_number' => 'STU-DUP-1']),
    );

    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['student_number' => 'STU-DUP-1']),
    );
})->throws(DuplicateStudentException::class);

test('it allows the same student number across schools', function (): void {
    $otherSchool = makeTenant();
    [$otherYear, $otherClass] = makeAcademicContext($otherSchool);

    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['student_number' => 'STU-SHARED-1']),
    );

    app(CreateStudentAction::class)->execute(
        createStudentData($otherSchool, $otherYear->id, $otherClass->id, ['student_number' => 'STU-SHARED-1']),
    );

    expect(Student::query()->forSchool($otherSchool)->where('student_number', 'STU-SHARED-1')->exists())->toBeTrue();
});

test('it rejects a duplicate email in the same school', function (): void {
    $data = createStudentData($this->schoolId, $this->year->id, $this->class->id, ['email' => 'jean@example.com']);

    app(CreateStudentAction::class)->execute($data);

    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['email' => 'JEAN@example.com']),
    );
})->throws(DuplicateStudentException::class);

test('it rejects a birth date in the future', function (): void {
    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['date_of_birth' => '2099-01-01']),
    );
})->throws(InvalidArgumentException::class);

test('it rejects an inactive academic year', function (): void {
    $inactiveYear = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $this->schoolId,
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'draft',
    ]);

    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $inactiveYear->id, $this->class->id),
    );
})->throws(AcademicAssignmentException::class);

test('it rejects a class from another school', function (): void {
    $otherSchool = makeTenant();
    [, $otherClass] = makeAcademicContext($otherSchool);

    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $otherClass->id),
    );
})->throws(AcademicAssignmentException::class);

test('it rejects an admission date before the academic year starts', function (): void {
    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['admission_date' => '2026-08-31']),
    );
})->throws(InvalidArgumentException::class);

test('it rejects an admission date after the academic year ends', function (): void {
    app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id, ['admission_date' => '2027-08-01']),
    );
})->throws(InvalidArgumentException::class);

test('it stores guardians and marks the first as primary', function (): void {
    $student = app(CreateStudentAction::class)->execute(
        createStudentData($this->schoolId, $this->year->id, $this->class->id)
            ->withGuardians([
                new GuardianReference('Mme Aya', '+2251111111111', 'aya@example.com', 'parent', false),
                new GuardianReference('M. Kouassi', '+2252222222222', null, 'guardian', true),
            ]),
    );

    expect($student->guardians()->count())->toBe(2)
        ->and($student->guardians()->where('is_primary', true)->count())->toBe(1)
        ->and($student->guardians()->where('is_primary', true)->first()->name)->toBe('M. Kouassi');
});
