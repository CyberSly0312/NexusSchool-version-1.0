<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Actions\PublishTimetableAction;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;
use App\Models\AuditLog;
use App\Models\User;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->actor = User::factory()->create(['school_id' => $this->schoolId]);
    $this->academic = makeTimetableAcademicContext($this->schoolId);
    $this->timetable = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
});

test('creating and scheduling a timetable writes audit log entries', function (): void {
    $created = app(\App\Domains\Timetable\Application\Actions\CreateTimetableAction::class)->execute(
        \App\Domains\Timetable\Application\DTOs\CreateTimetableData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'EDT Audit 2026',
        ]),
        $this->actor,
    );

    $entry = scheduleLesson($this->schoolId, $created->id, $this->academic['subject']->id, $this->academic['class']->id, actor: $this->actor);

    expect(AuditLog::where('auditable_type', Timetable::class)->where('auditable_id', $created->id)->first()?->action)
        ->toBe('timetable_created')
        ->and(AuditLog::where('auditable_type', \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::class)->where('auditable_id', $entry->id)->first()?->action)
        ->toBe('schedule_entry_created')
        ->and(AuditLog::first()->user_id)->toBe($this->actor->id);
});

test('publishing snapshots the schedule in an immutable version row', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    app(PublishTimetableAction::class)->execute($this->timetable->id, $this->actor);

    $version = TimetableVersion::forSchool($this->schoolId)->firstOrFail();

    expect($version->version)->toBe(2)
        ->and($version->snapshot)->toHaveCount(1)
        ->and($version->snapshot[0]['academic_subject_id'])->toBe($this->academic['subject']->id)
        ->and(AuditLog::where('action', 'timetable_published')->forSchool($this->schoolId)->count())->toBe(1);
});

test('conflict detection and resolution are audited', function (): void {
    makeTeachingGrid($this->schoolId);
    $teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);
    $otherClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);

    \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::create([
        'school_id' => $this->schoolId,
        'timetable_id' => $this->timetable->id,
        'academic_subject_id' => $this->academic['subject']->id,
        'school_class_id' => $this->academic['class']->id,
        'staff_id' => $teacher->id,
        'day_of_week' => 0,
        'period_number' => 1,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
        'duration_minutes' => 55,
        'entry_type' => 'lesson',
        'status' => 'scheduled',
    ]);
    \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::create([
        'school_id' => $this->schoolId,
        'timetable_id' => $this->timetable->id,
        'academic_subject_id' => $this->academic['subject']->id,
        'school_class_id' => $otherClass->id,
        'staff_id' => $teacher->id,
        'day_of_week' => 0,
        'period_number' => 1,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
        'duration_minutes' => 55,
        'entry_type' => 'lesson',
        'status' => 'scheduled',
    ]);

    $conflicts = app(\App\Domains\Timetable\Application\Actions\DetectConflictsAction::class)->execute($this->timetable->id, $this->actor);

    expect(AuditLog::where('action', 'conflict_detected')->forSchool($this->schoolId)->count())->toBeGreaterThanOrEqual(1);

    app(\App\Domains\Timetable\Application\Actions\ResolveConflictAction::class)->execute($conflicts->first()->id, 'resolved', null, $this->actor);

    expect(AuditLog::where('action', 'conflict_resolved')->forSchool($this->schoolId)->count())->toBe(1);
});

test('audit logs are isolated per school', function (): void {
    $otherSchool = makeTenant();

    $created = app(\App\Domains\Timetable\Application\Actions\CreateTimetableAction::class)->execute(
        \App\Domains\Timetable\Application\DTOs\CreateTimetableData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'EDT Isolé',
        ]),
        $this->actor,
    );

    expect(AuditLog::forSchool($this->schoolId)->count())->toBe(1)
        ->and(AuditLog::forSchool($otherSchool)->count())->toBe(0)
        ->and($created->status->value)->toBe('draft');
});
