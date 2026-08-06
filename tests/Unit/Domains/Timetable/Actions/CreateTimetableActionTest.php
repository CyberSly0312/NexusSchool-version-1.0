<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Actions\CreateTimetableAction;
use App\Domains\Timetable\Application\DTOs\CreateTimetableData;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\Timetable;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
});

test('creating a timetable produces a draft aggregate', function (): void {
    $timetable = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Emploi du temps 2026-2027',
    ]));

    expect($timetable)->toBeInstanceOf(Timetable::class)
        ->and($timetable->school_id)->toBe($this->schoolId)
        ->and($timetable->status)->toBe(TimetableStatus::Draft)
        ->and($timetable->version)->toBe(1)
        ->and($timetable->total_entries)->toBe(0);
});

test('creating a timetable rejects an empty name', function (): void {
    app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '   ',
    ]));
})->throws(InvalidTimetableStateException::class);

test('creating a timetable rejects duplicate names within the school', function (): void {
    $data = CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Emploi du temps 2026-2027',
    ]);

    app(CreateTimetableAction::class)->execute($data);
    app(CreateTimetableAction::class)->execute($data);
})->throws(InvalidTimetableStateException::class);

test('the same timetable name is allowed in different schools', function (): void {
    $otherSchool = makeTenant();

    $first = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'EDT Principal',
    ]));
    $second = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $otherSchool,
        'name' => 'EDT Principal',
    ]));

    expect($first->id)->not->toBe($second->id);
});

test('creating a timetable validates the academic year through the contract', function (): void {
    app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'EDT 2026',
        'academic_calendar_year_id' => 999999,
    ]));
})->throws(ContractViolationException::class);

test('creating a timetable validates the academic term through the contract', function (): void {
    app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'EDT 2026',
        'academic_calendar_term_id' => 999999,
    ]));
})->throws(ContractViolationException::class);

test('creating a timetable accepts a year and term of the school', function (): void {
    $academic = makeTimetableAcademicContext($this->schoolId);

    $timetable = app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'EDT 2026-2027',
        'academic_calendar_year_id' => $academic['year']->id,
        'academic_calendar_term_id' => $academic['term']->id,
    ]));

    expect($timetable->academic_calendar_year_id)->toBe($academic['year']->id)
        ->and($timetable->academic_calendar_term_id)->toBe($academic['term']->id);
});

test('a timetable cannot reference the year of another school', function (): void {
    $otherSchool = makeTenant();
    $foreignYear = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $otherSchool,
        'name' => '2025-2026',
        'code' => 'AY-2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'active',
    ]);

    app(CreateTimetableAction::class)->execute(CreateTimetableData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'EDT 2026',
        'academic_calendar_year_id' => $foreignYear->id,
    ]));
})->throws(ContractViolationException::class);
