<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\GraduateStudentAction;
use App\Domains\Enrollment\Application\Actions\PromoteStudentAction;
use App\Domains\Enrollment\Application\Actions\RestoreEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\SuspendEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\UpdateEnrollmentAction;
use App\Domains\Enrollment\Application\Actions\WithdrawStudentAction;
use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\DTOs\GraduationData;
use App\Domains\Enrollment\Application\DTOs\PromotionData;
use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\AuditLog;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class, $this->program, $this->level] = makeEnrollmentContext($this->schoolId);
    $this->student = Student::factory()->create(['school_id' => $this->schoolId]);
});

function auditEnrollmentData(object $context, object $student): CreateEnrollmentData
{
    return CreateEnrollmentData::fromArray([
        'school_id' => $context->schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $context->year->id,
        'academic_program_id' => $context->program->id,
        'education_level_id' => $context->level->id,
        'school_class_id' => $context->class->id,
        'enrolled_at' => '2026-09-15',
    ]);
}

test('registration writes an audit log entry', function (): void {
    $actor = User::factory()->create(['school_id' => $this->schoolId]);

    $enrollment = app(CreateEnrollmentAction::class)->execute(auditEnrollmentData($this, $this->student), $actor);

    $log = AuditLog::where('auditable_id', $enrollment->id)
        ->where('auditable_type', Enrollment::class)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('created')
        ->and($log->school_id)->toBe($this->schoolId)
        ->and($log->user_id)->toBe($actor->id);

    $history = EnrollmentHistory::where('enrollment_id', $enrollment->id)->first();

    expect($history)->not->toBeNull()
        ->and($history->event)->toBe('created')
        ->and($history->school_id)->toBe($this->schoolId);
});

test('updates and lifecycle actions are all audited and traced', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(auditEnrollmentData($this, $this->student));

    app(UpdateEnrollmentAction::class)->execute($enrollment, new UpdateEnrollmentData(notes: 'Notes'));
    app(SuspendEnrollmentAction::class)->execute($enrollment->fresh(), 'Retard répété');
    app(RestoreEnrollmentAction::class)->execute($enrollment->fresh());
    app(WithdrawStudentAction::class)->execute($enrollment->fresh(), 'Déménagement');

    $actions = AuditLog::where('auditable_id', $enrollment->id)
        ->where('auditable_type', Enrollment::class)
        ->orderBy('id')
        ->pluck('action')
        ->all();

    expect($actions)->toBe(['created', 'updated', 'suspended', 'restored', 'withdrawn']);

    $events = EnrollmentHistory::where('enrollment_id', $enrollment->id)
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($events)->toBe(['created', 'updated', 'suspended', 'restored', 'withdrawn']);
});

test('promotion and graduation append their dedicated history events', function (): void {
    $enrollment = app(CreateEnrollmentAction::class)->execute(auditEnrollmentData($this, $this->student));

    $nextYear = makeNextAcademicYear($this->schoolId, $this->year);

    $nextClass = SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    app(PromoteStudentAction::class)->execute($enrollment, new PromotionData(
        decision: PromotionDecision::Promoted,
        academicCalendarYearId: $nextYear->id,
        promotedOn: '2027-09-15',
        toClassId: $nextClass->id,
    ));

    $newRow = Enrollment::forSchool($this->schoolId)->forYear($nextYear->id)->first();

    app(GraduateStudentAction::class)->execute($newRow, new GraduationData(
        decision: GraduationDecision::Graduated,
        educationLevelId: $this->level->id,
        graduatedOn: '2027-07-01',
    ));

    expect(EnrollmentHistory::where('enrollment_id', $enrollment->id)->orderBy('id')->pluck('event')->all())
        ->toBe(['created', 'promoted']);

    expect(EnrollmentHistory::where('enrollment_id', $newRow->id)->orderBy('id')->pluck('event')->all())
        ->toBe(['created', 'graduated']);
});

test('audit logs and history are isolated per school', function (): void {
    $otherSchool = makeTenant();

    app(CreateEnrollmentAction::class)->execute(auditEnrollmentData($this, $this->student));

    expect(AuditLog::forSchool($this->schoolId)->count())->toBe(1)
        ->and(AuditLog::forSchool($otherSchool)->count())->toBe(0)
        ->and(EnrollmentHistory::forSchool($this->schoolId)->count())->toBe(1)
        ->and(EnrollmentHistory::forSchool($otherSchool)->count())->toBe(0);
});
