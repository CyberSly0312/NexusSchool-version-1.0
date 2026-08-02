<?php

declare(strict_types=1);

use App\Domains\Student\Application\Actions\ArchiveStudentAction;
use App\Domains\Student\Application\Actions\CreateStudentAction;
use App\Domains\Student\Application\Actions\GraduateStudentAction;
use App\Domains\Student\Application\Actions\RestoreStudentAction;
use App\Domains\Student\Application\Actions\SuspendStudentAction;
use App\Domains\Student\Application\Actions\UpdateStudentAction;
use App\Domains\Student\Application\DTOs\CreateStudentData;
use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\AuditLog;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    [$this->year, $this->class] = makeAcademicContext($this->schoolId);
});

function auditStudentData(string $schoolId, int $yearId, int $classId, array $overrides = []): CreateStudentData
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

test('registration writes an audit log entry', function (): void {
    $actor = User::factory()->create(['school_id' => $this->schoolId]);

    $student = app(CreateStudentAction::class)->execute(
        auditStudentData($this->schoolId, $this->year->id, $this->class->id),
        $actor,
    );

    $log = AuditLog::where('auditable_id', $student->id)->first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('created')
        ->and($log->school_id)->toBe($this->schoolId)
        ->and($log->user_id)->toBe($actor->id)
        ->and($log->auditable_type)->toBe(Student::class);
});

test('updates and lifecycle actions are all audited', function (): void {
    $student = app(CreateStudentAction::class)->execute(
        auditStudentData($this->schoolId, $this->year->id, $this->class->id),
    );

    app(UpdateStudentAction::class)->execute($student, new UpdateStudentData(firstName: 'Jean-Marc'));
    app(SuspendStudentAction::class)->execute($student->fresh(), 'Retard répété');
    app(ArchiveStudentAction::class)->execute($student->fresh());
    app(RestoreStudentAction::class)->execute($student->fresh());

    $actions = AuditLog::where('auditable_id', $student->id)->pluck('action')->all();

    expect($actions)->toBe([
        'created',
        'updated',
        'suspended',
        'archived',
        'restored',
    ]);

    $graduate = app(CreateStudentAction::class)->execute(
        auditStudentData($this->schoolId, $this->year->id, $this->class->id, ['student_number' => 'STU-GRAD-1']),
    );

    app(GraduateStudentAction::class)->execute($graduate);

    expect(AuditLog::where('auditable_id', $graduate->id)->orderBy('created_at')->pluck('action')->all())
        ->toBe(['created', 'graduated']);
});

test('audit logs are isolated per school', function (): void {
    $otherSchool = makeTenant();

    $student = app(CreateStudentAction::class)->execute(
        auditStudentData($this->schoolId, $this->year->id, $this->class->id),
    );

    expect(AuditLog::forSchool($this->schoolId)->count())->toBe(1)
        ->and(AuditLog::forSchool($otherSchool)->count())->toBe(0);
});
