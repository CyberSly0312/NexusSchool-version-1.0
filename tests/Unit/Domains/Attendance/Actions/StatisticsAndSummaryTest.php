<?php

declare(strict_types=1);

use App\Domains\Attendance\Application\Actions\CalculateAttendanceStatisticsAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\GenerateAttendanceSummaryAction;
use App\Domains\Attendance\Application\Actions\OpenAttendanceSessionAction;
use App\Domains\Attendance\Application\Actions\RecordAttendanceAction;
use App\Domains\Attendance\Application\DTOs\RecordAttendanceData;
use App\Domains\Attendance\Application\DTOs\SessionGenerationData;
use App\Domains\Attendance\Application\DTOs\StatisticsData;
use App\Domains\Attendance\Application\DTOs\SummaryData;
use App\Domains\Attendance\Application\Services\AttendanceAlertDispatcher;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\Events\AttendanceAlertGenerated;
use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makePublishedLesson($this->schoolId);
    $this->enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );
});

function makeSessionFor(object $test, string $date): object
{
    [$session] = app(GenerateAttendanceSessionAction::class)->execute(
        new SessionGenerationData(
            schoolId: $test->schoolId,
            classId: $test->context['class']->id,
            dates: [$date],
            academicYearId: $test->context['year']->id,
            academicTermId: $test->context['term']->id,
            timetableId: $test->context['timetable']->id,
        ),
    );

    app(OpenAttendanceSessionAction::class)->execute($session);

    return $session;
}

function recordFor(object $test, object $session, AttendanceStatus $status): void
{
    app(RecordAttendanceAction::class)->execute(
        new RecordAttendanceData(
            sessionId: $session->id,
            enrollmentId: $test->enrollment->id,
            status: $status,
        ),
    );
}

test('statistics compute percentages, streaks and a weighted risk score', function (): void {
    $sessions = [];
    foreach ([1, 2, 3, 4] as $week) {
        $sessions[] = makeSessionFor($this, attendanceMonday($week));
    }

    recordFor($this, $sessions[0], AttendanceStatus::Present);
    recordFor($this, $sessions[1], AttendanceStatus::Late);
    recordFor($this, $sessions[2], AttendanceStatus::Absent);
    recordFor($this, $sessions[3], AttendanceStatus::Absent);

    $result = app(CalculateAttendanceStatisticsAction::class)->execute(
        new StatisticsData(
            schoolId: $this->schoolId,
            enrollmentId: $this->enrollment->id,
            period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
        ),
    );

    expect($result->sessionsCount)->toBe(4)
        ->and($result->attendancePercentage)->toBe(50.0)
        ->and($result->latePercentage)->toBe(50.0)
        ->and($result->consecutiveAbsences)->toBe(2)
        ->and($result->consecutiveLates)->toBe(0)
        ->and($result->breakdown['present'])->toBe(1)
        ->and($result->breakdown['late'])->toBe(1)
        ->and($result->breakdown['absent'])->toBe(2)
        ->and($result->riskScore)->toBeGreaterThan(0.0);
});

test('statistics never persist anything', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Present);

    app(CalculateAttendanceStatisticsAction::class)->execute(
        new StatisticsData(
            schoolId: $this->schoolId,
            enrollmentId: $this->enrollment->id,
            period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
        ),
    );

    expect(AttendanceSummary::count())->toBe(0);
});

test('summary materializes the computed statistics for the period', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Present);

    $summary = app(GenerateAttendanceSummaryAction::class)->execute(
        new SummaryData(
            schoolId: $this->schoolId,
            enrollmentId: $this->enrollment->id,
            period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
            dispatchAlerts: false,
        ),
    );

    expect($summary->enrollment_id)->toBe($this->enrollment->id)
        ->and($summary->period_type)->toBe('monthly')
        ->and($summary->period_start->toDateString())->toBe('2026-09-01')
        ->and($summary->period_end->toDateString())->toBe('2026-09-30')
        ->and($summary->attendance_percentage)->toBe(100.0)
        ->and($summary->present_count)->toBe(1)
        ->and($summary->late_count)->toBe(0)
        ->and($summary->risk_score)->toBe(0.0)
        ->and($summary->calculated_at)->not->toBeNull();
});

test('summary regeneration upserts the same period row', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Present);

    $data = new SummaryData(
        schoolId: $this->schoolId,
        enrollmentId: $this->enrollment->id,
        period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
        dispatchAlerts: false,
    );

    app(GenerateAttendanceSummaryAction::class)->execute($data);

    $session2 = makeSessionFor($this, attendanceMonday(2));
    recordFor($this, $session2, AttendanceStatus::Absent);

    $updated = app(GenerateAttendanceSummaryAction::class)->execute($data);

    expect(AttendanceSummary::where('enrollment_id', $this->enrollment->id)->count())->toBe(1)
        ->and($updated->attendance_percentage)->toBe(50.0);
});

test('the alert engine raises an alert when attendance drops under the threshold', function (): void {
    Event::fake([AttendanceAlertGenerated::class, \App\Domains\Attendance\Domain\Events\LowAttendanceDetected::class]);

    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Absent);

    app(GenerateAttendanceSummaryAction::class)->execute(
        new SummaryData(
            schoolId: $this->schoolId,
            enrollmentId: $this->enrollment->id,
            period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
            dispatchAlerts: true,
        ),
    );

    $alert = AttendanceAlert::where('enrollment_id', $this->enrollment->id)
        ->where('alert_type', 'low_attendance')
        ->first();

    expect($alert)->not->toBeNull()
        ->and($alert->severity)->toBe('high')
        ->and($alert->status->value)->toBe('open')
        ->and($alert->isOpen())->toBeTrue()
        ->and($alert->observed_value)->toBe(0.0);

    Event::assertDispatched(AttendanceAlertGenerated::class);
    Event::assertDispatched(\App\Domains\Attendance\Domain\Events\LowAttendanceDetected::class);
});

test('a healthy attendance profile raises no alert', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Present);

    $alerts = app(AttendanceAlertDispatcher::class)->dispatch(
        schoolId: $this->schoolId,
        enrollmentId: $this->enrollment->id,
        metrics: [
            'attendance_percentage' => 100.0,
            'late_percentage' => 0.0,
            'consecutive_absences' => 0,
            'consecutive_lates' => 0,
            'risk_score' => 0.0,
        ],
        periodStart: '2026-09-01',
        periodEnd: '2026-09-30',
    );

    expect($alerts)->toBeEmpty()
        ->and(AttendanceAlert::count())->toBe(0);
});

test('an already open alert of the same type is not duplicated', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Absent);

    $data = new SummaryData(
        schoolId: $this->schoolId,
        enrollmentId: $this->enrollment->id,
        period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
        dispatchAlerts: true,
    );

    app(GenerateAttendanceSummaryAction::class)->execute($data);
    app(GenerateAttendanceSummaryAction::class)->execute($data);

    expect(AttendanceAlert::where('enrollment_id', $this->enrollment->id)
        ->where('alert_type', 'low_attendance')->count())->toBe(1);
});

test('alerts are scoped per school', function (): void {
    $session = makeSessionFor($this, attendanceMonday(1));
    recordFor($this, $session, AttendanceStatus::Absent);

    app(GenerateAttendanceSummaryAction::class)->execute(
        new SummaryData(
            schoolId: $this->schoolId,
            enrollmentId: $this->enrollment->id,
            period: AttendancePeriod::monthly(CarbonImmutable::parse('2026-09-15')),
            dispatchAlerts: true,
        ),
    );

    $otherSchool = makeTenant('Other School');
    expect(AttendanceAlert::forSchool($otherSchool)->count())->toBe(0);
});
