<?php

declare(strict_types=1);

use App\Domains\Student\Application\Actions\ArchiveStudentAction;
use App\Domains\Student\Application\Actions\GraduateStudentAction;
use App\Domains\Student\Application\Actions\ReactivateStudentAction;
use App\Domains\Student\Application\Actions\RestoreStudentAction;
use App\Domains\Student\Application\Actions\SuspendStudentAction;
use App\Domains\Student\Application\Actions\TransferStudentAction;
use App\Domains\Student\Application\Actions\WithdrawStudentAction;
use App\Domains\Student\Application\DTOs\TransferStudentData;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;
use App\Domains\Student\Domain\Events\StudentArchived;
use App\Domains\Student\Domain\Events\StudentGraduated;
use App\Domains\Student\Domain\Events\StudentRestored;
use App\Domains\Student\Domain\Events\StudentSuspended;
use App\Domains\Student\Domain\Events\StudentTransferred;
use App\Domains\Student\Domain\Events\StudentWithdrawn;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class] = makeAcademicContext($this->schoolId);
});

function activeStudent(string $schoolId, int $yearId, int $classId): Student
{
    return Student::factory()->create([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $yearId,
        'school_class_id' => $classId,
        'status' => StudentStatus::Active,
    ]);
}

test('it transfers an incoming student', function (): void {
    Event::fake([StudentTransferred::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    $result = app(TransferStudentAction::class)->execute(
        $student,
        new TransferStudentData(TransferStatus::Incoming, transferFromSchool: 'École des Sources'),
    );

    expect($result->transfer_status)->toBe(TransferStatus::Incoming)
        ->and($result->transfer_from_school)->toBe('École des Sources')
        ->and($result->transferred_at)->not->toBeNull();

    Event::assertDispatched(StudentTransferred::class);
});

test('it cannot transfer an archived student', function (): void {
    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);
    $student->update(['status' => StudentStatus::Archived]);

    app(TransferStudentAction::class)->execute(
        $student,
        new TransferStudentData(TransferStatus::Outgoing),
    );
})->throws(InvalidStudentStateException::class);

test('it suspends an active student', function (): void {
    Event::fake([StudentSuspended::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    $result = app(SuspendStudentAction::class)->execute($student, 'Mauvaise conduite', '2026-12-01');

    expect($result->status)->toBe(StudentStatus::Suspended)
        ->and($result->suspension_reason)->toBe('Mauvaise conduite')
        ->and($result->suspended_until?->toDateString())->toBe('2026-12-01');

    Event::assertDispatched(StudentSuspended::class);
});

test('it cannot suspend a non-active student', function (): void {
    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);
    $student->update(['status' => StudentStatus::Withdrawn]);

    app(SuspendStudentAction::class)->execute($student);
})->throws(InvalidStudentStateException::class);

test('it graduates an active student and records the date', function (): void {
    Event::fake([StudentGraduated::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    $result = app(GraduateStudentAction::class)->execute($student);

    expect($result->status)->toBe(StudentStatus::Graduated)
        ->and($result->graduated_at?->toDateString())->toBe(now()->toDateString());

    Event::assertDispatched(StudentGraduated::class);
});

test('it cannot graduate a student twice', function (): void {
    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);
    $student->update(['status' => StudentStatus::Graduated]);

    app(GraduateStudentAction::class)->execute($student);
})->throws(InvalidStudentStateException::class);

test('it withdraws an active student', function (): void {
    Event::fake([StudentWithdrawn::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    $result = app(WithdrawStudentAction::class)->execute($student, 'Déménagement');

    expect($result->status)->toBe(StudentStatus::Withdrawn)
        ->and($result->withdrawal_reason)->toBe('Déménagement')
        ->and($result->withdrawn_at)->not->toBeNull();

    Event::assertDispatched(StudentWithdrawn::class);
});

test('it reactivates a suspended student', function (): void {
    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);
    $student->update(['status' => StudentStatus::Suspended]);

    $result = app(ReactivateStudentAction::class)->execute($student);

    expect($result->status)->toBe(StudentStatus::Active)
        ->and($result->reactivated_at)->not->toBeNull();
});

test('it archives a student through a soft delete', function (): void {
    Event::fake([StudentArchived::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    $result = app(ArchiveStudentAction::class)->execute($student);

    expect($result->status)->toBe(StudentStatus::Archived)
        ->and($result->trashed())->toBeTrue()
        ->and($result->archived_at)->not->toBeNull();

    Event::assertDispatched(StudentArchived::class);
});

test('it restores an archived student', function (): void {
    Event::fake([StudentRestored::class]);

    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);
    app(ArchiveStudentAction::class)->execute($student);

    $result = app(RestoreStudentAction::class)->execute($student->fresh());

    expect($result->status)->toBe(StudentStatus::Active)
        ->and($result->trashed())->toBeFalse()
        ->and($result->archived_at)->toBeNull();

    Event::assertDispatched(StudentRestored::class);
});

test('it cannot restore a non-archived student', function (): void {
    $student = activeStudent($this->schoolId, $this->year->id, $this->class->id);

    app(RestoreStudentAction::class)->execute($student);
})->throws(InvalidStudentStateException::class);
