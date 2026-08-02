<?php

declare(strict_types=1);

use App\Domains\Academic\Application\DTOs\CalendarEventData;
use App\Domains\Academic\Application\DTOs\CalendarTermData;
use App\Domains\Academic\Application\DTOs\CalendarYearData;
use App\Domains\Academic\Application\Services\CalendarService;
use App\Domains\Academic\Domain\Enums\CalendarTermStatus;
use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Domain\Events\CalendarYearActivated;
use App\Domains\Academic\Domain\Exceptions\ActiveCalendarYearAlreadyExistsException;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Domain\Exceptions\OverlappingCalendarTermException;
use App\Domains\Academic\Domain\Exceptions\TermOutsideCalendarYearException;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->calendar = app(CalendarService::class);
});

test('creates a calendar year in draft', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));

    expect($year)
        ->toBeInstanceOf(AcademicCalendarYear::class)
        ->and($year->status)->toBe(CalendarYearStatus::Draft)
        ->and($year->school_id)->toBe($this->schoolId);
});

test('rejects a duplicate calendar year name per school', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));

    $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
})->throws(DuplicateResourceException::class);

test('activates a year and demotes the previous active one', function (): void {
    $previous = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
    ]));
    $this->calendar->activateYear($previous);

    $next = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($next);

    expect($previous->refresh()->status)->toBe(CalendarYearStatus::Draft)
        ->and($next->refresh()->status)->toBe(CalendarYearStatus::Active)
        ->and($this->calendar->getActiveYearForSchool($this->schoolId)->is($next))->toBeTrue();
});

test('reports the active year state for a school', function (): void {
    expect($this->calendar->hasActiveYearForSchool($this->schoolId))->toBeFalse();

    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($year);

    expect($this->calendar->hasActiveYearForSchool($this->schoolId))->toBeTrue();
});

test('rejects activating an already active year', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($year);

    $this->calendar->activateYear($year->fresh());
})->throws(ActiveCalendarYearAlreadyExistsException::class);

test('archives a year', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($year);

    $this->calendar->archiveYear($year);

    expect($year->refresh()->status)->toBe(CalendarYearStatus::Archived);
});

test('creates a term contained in the calendar year', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));

    $term = $this->calendar->createTerm(CalendarTermData::fromArray([
        'school_id' => $this->schoolId,
        'calendar_year_id' => $year->id,
        'name' => 'Trimestre 1',
        'starts_on' => '2026-09-01',
        'ends_on' => '2026-12-18',
    ]));

    expect($term)
        ->toBeInstanceOf(AcademicCalendarTerm::class)
        ->and($term->status)->toBe(CalendarTermStatus::Planned);
});

test('rejects a term outside the calendar year', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));

    $this->calendar->createTerm(CalendarTermData::fromArray([
        'school_id' => $this->schoolId,
        'calendar_year_id' => $year->id,
        'name' => 'Trimestre hors année',
        'starts_on' => '2027-08-01',
        'ends_on' => '2027-09-30',
    ]));
})->throws(TermOutsideCalendarYearException::class);

test('rejects overlapping terms in the same year', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->createTerm(CalendarTermData::fromArray([
        'school_id' => $this->schoolId,
        'calendar_year_id' => $year->id,
        'name' => 'Trimestre 1',
        'starts_on' => '2026-09-01',
        'ends_on' => '2026-12-18',
    ]));

    $this->calendar->createTerm(CalendarTermData::fromArray([
        'school_id' => $this->schoolId,
        'calendar_year_id' => $year->id,
        'name' => 'Trimestre 1 bis',
        'starts_on' => '2026-12-01',
        'ends_on' => '2027-01-15',
    ]));
})->throws(OverlappingCalendarTermException::class);

test('creates a calendar event', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));

    $event = $this->calendar->createEvent(CalendarEventData::fromArray([
        'school_id' => $this->schoolId,
        'calendar_year_id' => $year->id,
        'title' => 'Vacances de Noël',
        'starts_on' => '2026-12-20',
        'ends_on' => '2027-01-02',
    ]));

    expect($event)->toBeInstanceOf(AcademicCalendarEvent::class);
});

test('dispatches activation event', function (): void {
    Event::fake([CalendarYearActivated::class]);

    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($year);

    Event::assertDispatched(CalendarYearActivated::class);
});

test('writes an audit trail for calendar operations', function (): void {
    $year = $this->calendar->createYear(CalendarYearData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]));
    $this->calendar->activateYear($year);

    expect(App\Models\AuditLog::query()->forSchool($this->schoolId)->count())->toBe(2)
        ->and(App\Models\AuditLog::query()->forSchool($this->schoolId)->where('action', 'created')->exists())->toBeTrue()
        ->and(App\Models\AuditLog::query()->forSchool($this->schoolId)->where('action', 'activated')->exists())->toBeTrue();
});
