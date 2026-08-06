<?php

declare(strict_types=1);

use App\Domains\Attendance\Application\Actions\GenerateAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\LockAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\OpenAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\UnlockAttendanceSessionAction;
use App\Domains\Attendance\Application\DTOs\SessionGenerationData;
use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Events\AttendanceLocked;
use App\Domains\Attendance\Domain\Events\AttendanceSessionCreated;
use App\Domains\Attendance\Domain\Events\AttendanceSessionOpened;
use App\Domains\Attendance\Domain\Events\AttendanceUnlocked;
use App\Domains\Attendance\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Attendance\Domain\Exceptions\SessionAlreadyExistsException;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makePublishedLesson($this->schoolId);
    $this->date = attendanceMonday(1);

    $this->generate = function (string $date): array {
        return app(GenerateAttendanceSessionAction::class)->execute(
            new SessionGenerationData(
                schoolId: $this->schoolId,
                classId: $this->context['class']->id,
                dates: [$date],
                academicYearId: $this->context['year']->id,
                academicTermId: $this->context['term']->id,
                timetableId: $this->context['timetable']->id,
            ),
        );
    };
});

test('generation creates one draft session per class and slot for each date', function (): void {
    Event::fake([AttendanceSessionCreated::class]);

    $sessions = app(GenerateAttendanceSessionAction::class)->execute(
        new SessionGenerationData(
            schoolId: $this->schoolId,
            classId: $this->context['class']->id,
            dates: [attendanceMonday(1), attendanceMonday(2)],
            academicYearId: $this->context['year']->id,
            academicTermId: $this->context['term']->id,
            timetableId: $this->context['timetable']->id,
        ),
    );

    expect($sessions)->toHaveCount(2)
        ->and($sessions[0]->status)->toBe(AttendanceSessionStatus::Draft)
        ->and($sessions[0]->date->toDateString())->toBe(attendanceMonday(1))
        ->and($sessions[0]->school_class_id)->toBe($this->context['class']->id)
        ->and($sessions[0]->day_of_week)->toBe(0)
        ->and($sessions[0]->period_number)->toBe(1)
        ->and($sessions[0]->starts_at)->toBe('08:00');

    Event::assertDispatched(AttendanceSessionCreated::class, 2);
});

test('generation records the enrollment cohort size as expected count', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $sessions = ($this->generate)($this->date);

    expect($sessions[0]->expected_students_count)->toBe(1)
        ->and($sessions[0]->refresh()->recorded_students_count)->toBe(0);
});

test('generation is rejected when no dates are provided', function (): void {
    app(GenerateAttendanceSessionAction::class)->execute(
        new SessionGenerationData(
            schoolId: $this->schoolId,
            classId: $this->context['class']->id,
        ),
    );
})->throws(\App\Domains\Attendance\Domain\Exceptions\ContractViolationException::class);

test('generation is rejected for an unknown class', function (): void {
    app(GenerateAttendanceSessionAction::class)->execute(
        new SessionGenerationData(
            schoolId: $this->schoolId,
            classId: 999999,
            dates: [$this->date],
        ),
    );
})->throws(\App\Domains\Attendance\Domain\Exceptions\ContractViolationException::class);

test('generation refuses to duplicate an existing session for the same slot', function (): void {
    ($this->generate)($this->date);

    ($this->generate)($this->date);
})->throws(SessionAlreadyExistsException::class);

test('an open session can be opened and records its opening time', function (): void {
    [$session] = ($this->generate)($this->date);

    Event::fake([AttendanceSessionOpened::class]);

    $opened = app(OpenAttendanceSessionAction::class)->execute($session);

    expect($opened->status)->toBe(AttendanceSessionStatus::Open)
        ->and($opened->opened_at)->not->toBeNull();

    Event::assertDispatched(AttendanceSessionOpened::class);
});

test('a draft session cannot be locked directly', function (): void {
    [$session] = ($this->generate)($this->date);

    app(LockAttendanceSessionAction::class)->execute($session);
})->throws(InvalidStatusTransitionException::class);

test('a submitted session can be locked and becomes immutable', function (): void {
    [$session] = ($this->generate)($this->date);

    Event::fake([AttendanceLocked::class]);

    app(OpenAttendanceSessionAction::class)->execute($session);
    $session->status = AttendanceSessionStatus::Submitted;
    $session->save();

    $locked = app(LockAttendanceSessionAction::class)->execute($session->refresh());

    expect($locked->status)->toBe(AttendanceSessionStatus::Locked)
        ->and($locked->locked_at)->not->toBeNull()
        ->and($locked->isImmutable())->toBeTrue();

    Event::assertDispatched(AttendanceLocked::class);
});

test('unlocking a locked session reverts it to submitted and clears the lock time', function (): void {
    [$session] = ($this->generate)($this->date);

    app(OpenAttendanceSessionAction::class)->execute($session);
    $session->status = AttendanceSessionStatus::Submitted;
    $session->save();
    $locked = app(LockAttendanceSessionAction::class)->execute($session->refresh());

    Event::fake([AttendanceUnlocked::class]);

    $unlocked = app(UnlockAttendanceSessionAction::class)->execute($locked);

    expect($unlocked->status)->toBe(AttendanceSessionStatus::Submitted)
        ->and($unlocked->locked_at)->toBeNull();

    Event::assertDispatched(AttendanceUnlocked::class);
});

test('only a locked session can be unlocked', function (): void {
    [$session] = ($this->generate)($this->date);

    app(OpenAttendanceSessionAction::class)->execute($session);

    app(UnlockAttendanceSessionAction::class)->execute($session->refresh());
})->throws(InvalidStatusTransitionException::class);

test('the lifecycle state machine is exercised end to end', function (): void {
    [$session] = ($this->generate)($this->date);

    app(OpenAttendanceSessionAction::class)->execute($session);
    $session->status = AttendanceSessionStatus::TakingAttendance;
    $session->save();
    $session->status = AttendanceSessionStatus::Submitted;
    $session->save();

    $locked = app(LockAttendanceSessionAction::class)->execute($session->refresh());
    $locked->status = AttendanceSessionStatus::Archived;
    $locked->save();

    expect($locked->refresh()->status)->toBe(AttendanceSessionStatus::Archived);
});

test('sessions are scoped to their school', function (): void {
    [$session] = ($this->generate)($this->date);

    $otherSchool = makeTenant('Other School');

    expect(AttendanceSession::forSchool($otherSchool)->count())->toBe(0)
        ->and(AttendanceSession::forSchool($this->schoolId)->count())->toBe(1)
        ->and($session->school_id)->toBe($this->schoolId);
});
