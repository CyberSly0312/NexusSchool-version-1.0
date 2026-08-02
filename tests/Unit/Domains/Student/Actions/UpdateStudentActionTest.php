<?php

declare(strict_types=1);

use App\Domains\Student\Application\Actions\UpdateStudentAction;
use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentUpdated;
use App\Domains\Student\Domain\Exceptions\DuplicateStudentException;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class] = makeAcademicContext($this->schoolId);
});

test('it updates student fields and dispatches an event', function (): void {
    Event::fake([StudentUpdated::class]);

    $student = Student::factory()->create(['school_id' => $this->schoolId, 'status' => StudentStatus::Active]);

    $result = app(UpdateStudentAction::class)->execute(
        $student,
        new UpdateStudentData(firstName: 'Marie', lastName: 'Kouassi', email: 'marie@example.com'),
    );

    expect($result->first_name)->toBe('Marie')
        ->and($result->last_name)->toBe('Kouassi')
        ->and($result->email)->toBe('marie@example.com');

    Event::assertDispatched(StudentUpdated::class);
});

test('it rejects updates to an archived student', function (): void {
    $student = Student::factory()->create([
        'school_id' => $this->schoolId,
        'status' => StudentStatus::Archived,
    ]);

    app(UpdateStudentAction::class)->execute($student, new UpdateStudentData(firstName: 'Marie'));
})->throws(InvalidStudentStateException::class);

test('it rejects an email already used by another student', function (): void {
    Student::factory()->create(['school_id' => $this->schoolId, 'email' => 'taken@example.com']);

    $student = Student::factory()->create(['school_id' => $this->schoolId]);

    app(UpdateStudentAction::class)->execute(
        $student,
        new UpdateStudentData(email: 'TAKEN@example.com'),
    );
})->throws(DuplicateStudentException::class);

test('it allows keeping the same email on update', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId, 'email' => 'same@example.com']);

    $result = app(UpdateStudentAction::class)->execute(
        $student,
        new UpdateStudentData(email: 'same@example.com'),
    );

    expect($result->email)->toBe('same@example.com');
});

test('it rejects a class from another school', function (): void {
    $otherSchool = makeTenant();
    [, $otherClass] = makeAcademicContext($otherSchool);

    $student = Student::factory()->create(['school_id' => $this->schoolId, 'status' => StudentStatus::Active]);

    app(UpdateStudentAction::class)->execute(
        $student,
        new UpdateStudentData(schoolClassId: $otherClass->id),
    );
})->throws(\App\Domains\Student\Domain\Exceptions\AcademicAssignmentException::class);

test('it replaces guardians when requested', function (): void {
    $student = Student::factory()->create(['school_id' => $this->schoolId, 'status' => StudentStatus::Active]);

    app(UpdateStudentAction::class)->execute(
        $student,
        new UpdateStudentData(replaceGuardians: true, guardians: [
            new GuardianReference('Mme Aya', '+2251111111111', null, 'parent', true),
        ]),
    );

    expect($student->guardians()->count())->toBe(1)
        ->and($student->guardians()->first()->name)->toBe('Mme Aya');
});
