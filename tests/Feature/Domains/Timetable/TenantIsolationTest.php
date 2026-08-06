<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Actions\CreateTimetableAction;
use App\Domains\Timetable\Application\DTOs\CreateTimetableData;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;

test('timetables of different schools never leak through queries', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $a = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $schoolA,
        'name' => 'EDT Ecole A',
    ]));
    $b = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $schoolB,
        'name' => 'EDT Ecole B',
    ]));

    expect(Timetable::forSchool($schoolA)->count())->toBe(1)
        ->and(Timetable::forSchool($schoolB)->count())->toBe(1)
        ->and(Timetable::forSchool($schoolA)->first()->id)->toBe($a->id)
        ->and(Timetable::forSchool($schoolB)->first()->id)->toBe($b->id);
});

test('an action refuses a timetable of another school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $timetableA = makeTimetable($schoolA);
    $academic = makeTimetableAcademicContext($schoolB);

    app(\App\Domains\Timetable\Application\Actions\CreateScheduleEntryAction::class)->execute(
        \App\Domains\Timetable\Application\DTOs\ScheduleEntryData::fromArray([
            'school_id' => $schoolB,
            'timetable_id' => $timetableA->id,
            'academic_subject_id' => $academic['subject']->id,
            'school_class_id' => $academic['class']->id,
        ]),
    );
})->throws(InvalidTimetableStateException::class);

test('the audit trail keeps a timetable of school A out of school B', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $academic = makeTimetableAcademicContext($schoolA);
    $timetableA = makeTimetable($schoolA, $academic['year']->id, $academic['term']->id);

    scheduleLesson($schoolA, $timetableA->id, $academic['subject']->id, $academic['class']->id);
    app(\App\Domains\Timetable\Application\Actions\PublishTimetableAction::class)->execute($timetableA->id);

    expect(TimetableVersion::forSchool($schoolA)->count())->toBe(1)
        ->and(TimetableVersion::forSchool($schoolB)->count())->toBe(0);
});

test('schedule entries are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $academicA = makeTimetableAcademicContext($schoolA);
    $academicB = makeTimetableAcademicContext($schoolB);

    $timetableA = makeTimetable($schoolA, $academicA['year']->id, $academicA['term']->id);
    $timetableB = makeTimetable($schoolB, $academicB['year']->id, $academicB['term']->id);

    scheduleLesson($schoolA, $timetableA->id, $academicA['subject']->id, $academicA['class']->id);
    scheduleLesson($schoolB, $timetableB->id, $academicB['subject']->id, $academicB['class']->id);

    expect(ScheduleEntry::forSchool($schoolA)->count())->toBe(1)
        ->and(ScheduleEntry::forSchool($schoolB)->count())->toBe(1)
        ->and(ScheduleEntry::forSchool($schoolA)->first()->timetable_id)->toBe($timetableA->id);
});

test('conflict rows are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $conflictA = \App\Domains\Timetable\Infrastructure\Models\TimetableConflict::factory()
        ->forSchool($schoolA)
        ->create();
    \App\Domains\Timetable\Infrastructure\Models\TimetableConflict::factory()
        ->forSchool($schoolB)
        ->create();

    expect(\App\Domains\Timetable\Infrastructure\Models\TimetableConflict::forSchool($schoolA)->count())->toBe(1)
        ->and(\App\Domains\Timetable\Infrastructure\Models\TimetableConflict::forSchool($schoolA)->first()->id)->toBe($conflictA->id);
});
