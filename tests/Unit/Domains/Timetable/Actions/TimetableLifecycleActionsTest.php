<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Actions\ArchiveTimetableAction;
use App\Domains\Timetable\Application\Actions\DetectConflictsAction;
use App\Domains\Timetable\Application\Actions\DuplicateTimetableAction;
use App\Domains\Timetable\Application\Actions\GenerateTimetableAction;
use App\Domains\Timetable\Application\Actions\PublishTimetableAction;
use App\Domains\Timetable\Application\DTOs\GenerateTimetableData;
use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->academic = makeTimetableAcademicContext($this->schoolId);
    $this->timetable = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
});

test('publishing bumps the version and stores an immutable snapshot', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $published = app(PublishTimetableAction::class)->execute($this->timetable->id);

    expect($published->status)->toBe(TimetableStatus::Published)
        ->and($published->version)->toBe(2)
        ->and($published->published_at)->not->toBeNull()
        ->and(TimetableVersion::forSchool($this->schoolId)->count())->toBe(1)
        ->and(TimetableVersion::forSchool($this->schoolId)->first()->version)->toBe(2);
});

test('an empty timetable cannot be published', function (): void {
    app(PublishTimetableAction::class)->execute($this->timetable->id);
})->throws(ScheduleEntryException::class);

test('only a draft timetable can be published', function (): void {
    $published = \App\Domains\Timetable\Infrastructure\Models\Timetable::factory()
        ->forSchool($this->schoolId)
        ->published()
        ->create();

    app(PublishTimetableAction::class)->execute($published->id);
})->throws(InvalidTimetableStateException::class);

test('only one published timetable is allowed per academic period', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $second = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
    scheduleLesson($this->schoolId, $second->id, $this->academic['subject']->id, $this->academic['class']->id);

    app(PublishTimetableAction::class)->execute($this->timetable->id);

    app(PublishTimetableAction::class)->execute($second->id);
})->throws(InvalidTimetableStateException::class);

test('archiving frees the period so a new timetable can be published', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    app(PublishTimetableAction::class)->execute($this->timetable->id);
    app(ArchiveTimetableAction::class)->execute($this->timetable->id);

    $replacement = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
    scheduleLesson($this->schoolId, $replacement->id, $this->academic['subject']->id, $this->academic['class']->id);

    $published = app(PublishTimetableAction::class)->execute($replacement->id);

    expect($published->status)->toBe(TimetableStatus::Published);
});

test('an already archived timetable cannot be archived again', function (): void {
    $archived = \App\Domains\Timetable\Infrastructure\Models\Timetable::factory()
        ->forSchool($this->schoolId)
        ->archived()
        ->create();

    app(ArchiveTimetableAction::class)->execute($archived->id);
})->throws(InvalidTimetableStateException::class);

test('duplicating copies entries, slots and patterns as a draft', function (): void {
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $copy = app(DuplicateTimetableAction::class)->execute($this->timetable->id);

    expect($copy->status)->toBe(TimetableStatus::Draft)
        ->and($copy->name)->toBe($this->timetable->name.' (copie)')
        ->and($copy->total_entries)->toBe(1)
        ->and($copy->entries()->count())->toBe(1)
        ->and($copy->entries()->first()->academic_subject_id)->toBe($this->academic['subject']->id);
});

test('the generator fills the teaching grid deterministically', function (): void {
    makeTeachingGrid($this->schoolId);
    $teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);

    app(GenerateTimetableAction::class)->execute(new GenerateTimetableData(
        schoolId: $this->schoolId,
        timetableId: $this->timetable->id,
        classIds: [$this->academic['class']->id],
        requirements: [(string) $this->academic['subject']->id => 2],
    ));

    $entries = $this->timetable->refresh()->entries()->where('status', 'scheduled')->get();

    expect($entries)->toHaveCount(2)
        ->and($entries->pluck('staff_id')->unique()->first())->toBe($teacher->id)
        ->and($entries->pluck('day_of_week')->sort()->values()->all())->toBe([0, 0])
        ->and($entries->pluck('period_number')->sort()->values()->all())->toBe([1, 2]);
});

test('the generator rejects a draftless timetable', function (): void {
    $published = \App\Domains\Timetable\Infrastructure\Models\Timetable::factory()
        ->forSchool($this->schoolId)
        ->published()
        ->create();

    app(GenerateTimetableAction::class)->execute(new GenerateTimetableData(
        schoolId: $this->schoolId,
        timetableId: $published->id,
    ));
})->throws(InvalidTimetableStateException::class);

test('conflict detection persists evidence rows with a shared detection id', function (): void {
    makeTeachingGrid($this->schoolId);
    $teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);
    $otherClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '5ème A',
        'code' => 'CL5A',
        'status' => 'active',
    ]);
    $thirdClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->academic['program']->id,
        'name' => '5ème B',
        'code' => 'CL5B',
        'status' => 'active',
    ]);

    foreach ([$this->academic['class']->id, $otherClass->id, $thirdClass->id] as $classId) {
        \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::create([
            'school_id' => $this->schoolId,
            'timetable_id' => $this->timetable->id,
            'academic_subject_id' => $this->academic['subject']->id,
            'school_class_id' => $classId,
            'staff_id' => $teacher->id,
            'day_of_week' => 0,
            'period_number' => 1,
            'starts_at' => '08:00',
            'ends_at' => '08:55',
            'duration_minutes' => 55,
            'entry_type' => 'lesson',
            'status' => 'scheduled',
        ]);
    }

    $conflicts = app(DetectConflictsAction::class)->execute($this->timetable->id);

    expect($conflicts)->toHaveCount(3)
        ->and($conflicts->pluck('detection_id')->unique()->count())->toBe(1)
        ->and(TimetableConflict::forSchool($this->schoolId)->count())->toBe(3)
        ->and(TimetableConflict::forSchool($this->schoolId)->pluck('conflict_type')->unique()->all())->toBe([ConflictType::TeacherConflict]);
});

test('a clean timetable reports no conflicts', function (): void {
    makeTeachingGrid($this->schoolId);
    $teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);

    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $teacher->id);
    scheduleLesson($this->schoolId, $this->timetable->id, $this->academic['subject']->id, $this->academic['class']->id, $teacher->id, day: 1);

    $conflicts = app(DetectConflictsAction::class)->execute($this->timetable->id);

    expect($conflicts)->toHaveCount(0);
});
