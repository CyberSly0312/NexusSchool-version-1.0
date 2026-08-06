<?php

declare(strict_types=1);

use App\Domains\Attendance\Application\Actions\BulkAttendanceAction;
use App\Domains\Attendance\Application\Actions\CorrectAttendanceAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\OpenAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\RecordAttendanceAction;
use App\Domains\Attendance\Application\DTOs\BulkAttendanceData;
use App\Domains\Attendance\Application\DTOs\BulkAttendanceItem;
use App\Domains\Attendance\Application\DTOs\CorrectionData;
use App\Domains\Attendance\Application\DTOs\RecordAttendanceData;
use App\Domains\Attendance\Application\DTOs\SessionGenerationData;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\Events\AttendanceRecorded;
use App\Domains\Attendance\Domain\Exceptions\DuplicateRecordException;
use App\Domains\Attendance\Domain\Exceptions\SessionNotOpenException;
use App\Domains\Attendance\Domain\Exceptions\StudentNotEnrolledException;
use App\Domains\Attendance\Domain\ValueObjects\CorrectionReason;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\EarlyDeparture;
use App\Domains\Attendance\Infrastructure\Models\LateArrival;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makePublishedLesson($this->schoolId);
    $this->date = attendanceMonday(1);

    $this->enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );
    $this->otherEnrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    [$this->session] = app(GenerateAttendanceSessionAction::class)->execute(
        new SessionGenerationData(
            schoolId: $this->schoolId,
            classId: $this->context['class']->id,
            dates: [$this->date],
            academicYearId: $this->context['year']->id,
            academicTermId: $this->context['term']->id,
            timetableId: $this->context['timetable']->id,
        ),
    );

    app(OpenAttendanceSessionAction::class)->execute($this->session);
});

function recordPresence(object $test, int|string $enrollmentId, AttendanceStatus $status = AttendanceStatus::Present, ?int $lateMinutes = null): AttendanceRecord
{
    return app(RecordAttendanceAction::class)->execute(
        new RecordAttendanceData(
            sessionId: $test->session->id,
            enrollmentId: $enrollmentId,
            status: $status,
            lateMinutes: $lateMinutes,
        ),
    );
}

test('a present student is recorded once and increments the session counter', function (): void {
    Event::fake([AttendanceRecorded::class]);

    $record = recordPresence($this, $this->enrollment->id);

    expect($record->status)->toBe(AttendanceStatus::Present)
        ->and($record->attendance_session_id)->toBe($this->session->id)
        ->and($record->enrollment_id)->toBe($this->enrollment->id)
        ->and($record->school_id)->toBe($this->schoolId)
        ->and($this->session->refresh()->recorded_students_count)->toBe(1);

    Event::assertDispatched(AttendanceRecorded::class, 1);
});

test('recording the same student twice is rejected', function (): void {
    recordPresence($this, $this->enrollment->id);

    recordPresence($this, $this->enrollment->id);
})->throws(DuplicateRecordException::class);

test('a student not enrolled in the session class is rejected', function (): void {
    $otherClassSchool = makeTenant('Other School');
    $otherContext = makePublishedLesson($otherClassSchool);
    $stray = enrollStudent(
        $otherClassSchool,
        $otherContext['year']->id,
        $otherContext['program']->id,
        $otherContext['level']->id,
        $otherContext['class']->id,
    );

    recordPresence($this, $stray->id);
})->throws(StudentNotEnrolledException::class);

test('a suspended enrollment cannot be recorded', function (): void {
    $this->enrollment->update(['status' => \App\Domains\Enrollment\Domain\Enums\EnrollmentStatus::Suspended]);

    recordPresence($this, $this->enrollment->id);
})->throws(StudentNotEnrolledException::class);

test('recording requires an open session', function (): void {
    $this->session->update(['status' => \App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus::Submitted]);

    recordPresence($this, $this->enrollment->id);
})->throws(SessionNotOpenException::class);

test('a late record stores late minutes and creates a late arrival entry', function (): void {
    $record = app(RecordAttendanceAction::class)->execute(
        new RecordAttendanceData(
            sessionId: $this->session->id,
            enrollmentId: $this->enrollment->id,
            status: AttendanceStatus::Late,
            checkInTime: \Carbon\CarbonImmutable::parse('2026-09-07 08:15:00'),
            lateMinutes: 15,
        ),
    );

    $lateArrival = LateArrival::where('attendance_record_id', $record->id)->first();

    expect($record->late_minutes)->toBe(15)
        ->and($lateArrival)->not->toBeNull()
        ->and($lateArrival->minutes)->toBe(15)
        ->and($lateArrival->arrived_at->format('H:i'))->toBe('08:15');
});

test('a check-out creates an early departure entry', function (): void {
    $record = app(RecordAttendanceAction::class)->execute(
        new RecordAttendanceData(
            sessionId: $this->session->id,
            enrollmentId: $this->enrollment->id,
            status: AttendanceStatus::Present,
            checkOutTime: \Carbon\CarbonImmutable::parse('2026-09-07 08:50:00'),
        ),
    );

    $departure = EarlyDeparture::where('attendance_record_id', $record->id)->first();

    expect($departure)->not->toBeNull()
        ->and($departure->departed_at->toDateString())->toBe('2026-09-07');
});

test('bulk recording inserts only valid, unrecorded enrollments', function (): void {
    $result = app(BulkAttendanceAction::class)->execute(
        new BulkAttendanceData($this->session->id, [
            new BulkAttendanceItem($this->enrollment->id, 'present'),
            new BulkAttendanceItem($this->otherEnrollment->id, 'late', null, null, 10),
            new BulkAttendanceItem(999999, 'absent'),
        ]),
    );

    expect($result)->toMatchArray(['inserted' => 2, 'skipped' => 0, 'rejected' => 1])
        ->and(AttendanceRecord::where('attendance_session_id', $this->session->id)->count())->toBe(2)
        ->and($this->session->refresh()->recorded_students_count)->toBe(2);
});

test('bulk recording skips enrollments already recorded', function (): void {
    recordPresence($this, $this->enrollment->id);

    $result = app(BulkAttendanceAction::class)->execute(
        new BulkAttendanceData($this->session->id, [
            new BulkAttendanceItem($this->enrollment->id, 'present'),
            new BulkAttendanceItem($this->otherEnrollment->id, 'present'),
        ]),
    );

    expect($result)->toMatchArray(['inserted' => 1, 'skipped' => 1, 'rejected' => 0]);
});

test('bulk recording on a locked session is rejected', function (): void {
    $this->session->update(['status' => \App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus::Locked]);

    app(BulkAttendanceAction::class)->execute(
        new BulkAttendanceData($this->session->id, [
            new BulkAttendanceItem($this->enrollment->id, 'present'),
        ]),
    );
})->throws(SessionNotOpenException::class);

test('an authorized correction appends an audit entry and updates the record', function (): void {
    $record = recordPresence($this, $this->enrollment->id);

    Event::fake([\App\Domains\Attendance\Domain\Events\AttendanceCorrected::class]);

    $correction = app(CorrectAttendanceAction::class)->execute(
        new CorrectionData(
            recordId: $record->id,
            newStatus: AttendanceStatus::Absent,
            reason: CorrectionReason::forType(CorrectionReason::TYPE_ERROR, 'Typo'),
            authorizedBy: 42,
        ),
    );

    expect($correction->previous_status)->toBe(AttendanceStatus::Present)
        ->and($correction->new_status)->toBe(AttendanceStatus::Absent)
        ->and($correction->reason_type)->toBe('data_entry_error')
        ->and($correction->reason_text)->toBe('Typo')
        ->and($correction->authorized_by)->toBe(42)
        ->and($correction->is_authorized)->toBeTrue()
        ->and($record->fresh()->status)->toBe(AttendanceStatus::Absent);

    Event::assertDispatched(\App\Domains\Attendance\Domain\Events\AttendanceCorrected::class, 1);
});

test('a correction without authorization is rejected', function (): void {
    $record = recordPresence($this, $this->enrollment->id);

    app(CorrectAttendanceAction::class)->execute(
        new CorrectionData(
            recordId: $record->id,
            newStatus: AttendanceStatus::Absent,
            reason: new CorrectionReason(),
        ),
    );
})->throws(\App\Domains\Attendance\Domain\Exceptions\UnauthorizedCorrectionException::class);

test('a no-op correction is rejected', function (): void {
    $record = recordPresence($this, $this->enrollment->id);

    app(CorrectAttendanceAction::class)->execute(
        new CorrectionData(
            recordId: $record->id,
            newStatus: AttendanceStatus::Present,
            reason: new CorrectionReason(),
            authorizedBy: 42,
        ),
    );
})->throws(\App\Domains\Attendance\Domain\Exceptions\AttendanceException::class);

test('a correction on an archived session is rejected', function (): void {
    $record = recordPresence($this, $this->enrollment->id);
    $this->session->update(['status' => \App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus::Archived]);

    app(CorrectAttendanceAction::class)->execute(
        new CorrectionData(
            recordId: $record->id,
            newStatus: AttendanceStatus::Absent,
            reason: new CorrectionReason(),
            authorizedBy: 42,
        ),
    );
})->throws(\App\Domains\Attendance\Domain\Exceptions\SessionLockedException::class);

test('successive corrections accumulate an audit trail', function (): void {
    $record = recordPresence($this, $this->enrollment->id, AttendanceStatus::Present);

    app(CorrectAttendanceAction::class)->execute(new CorrectionData($record->id, AttendanceStatus::Late, new CorrectionReason(), authorizedBy: 1));
    app(CorrectAttendanceAction::class)->execute(new CorrectionData($record->id, AttendanceStatus::Excused, new CorrectionReason(), authorizedBy: 2));

    $corrections = AttendanceCorrection::where('attendance_record_id', $record->id)
        ->orderBy('id')
        ->get();

    expect($corrections)->toHaveCount(2)
        ->and($corrections[0]->previous_status)->toBe(AttendanceStatus::Present)
        ->and($corrections[0]->new_status)->toBe(AttendanceStatus::Late)
        ->and($corrections[1]->previous_status)->toBe(AttendanceStatus::Late)
        ->and($corrections[1]->new_status)->toBe(AttendanceStatus::Excused)
        ->and($record->fresh()->status)->toBe(AttendanceStatus::Excused);
});
