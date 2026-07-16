<?php

namespace Tests\Unit\Domains\AcademicYear;

use App\Domains\AcademicYear\DTOs\AcademicYearData;
use App\Domains\AcademicYear\Enums\AcademicYearStatus;
use App\Domains\AcademicYear\Events\AcademicYearActivated;
use App\Domains\AcademicYear\Events\AcademicYearCloned;
use App\Domains\AcademicYear\Events\AcademicYearCreated;
use App\Domains\AcademicYear\Events\AcademicYearDatesChanged;
use App\Domains\AcademicYear\Events\AcademicYearStatusChanged;
use App\Domains\AcademicYear\Events\AcademicYearSwitched;
use App\Domains\AcademicYear\Services\AcademicYearService;
use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicYearServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicYearService $service;

    protected string $schoolId;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AcademicYearService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school',
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->schoolId = $schoolId;
        $this->school = School::find($schoolId);
    }

    public function test_can_create_academic_year(): void
    {
        $data = AcademicYearData::fromArray([
            'school_id' => $this->school->id,
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'code' => 'AY-2526',
        ]);

        $academicYear = $this->service->create($data);

        $this->assertInstanceOf(AcademicYear::class, $academicYear);
        $this->assertEquals('2025-2026', $academicYear->name);
        $this->assertEquals('AY-2526', $academicYear->code);
        $this->assertEquals(AcademicYearStatus::Draft, $academicYear->status);
        $this->assertEquals($this->school->id, $academicYear->school_id);
    }

    public function test_can_update_academic_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create(['name' => '2025-2026']);

        $updated = $this->service->update($academicYear, ['name' => '2026-2027']);

        $this->assertEquals('2026-2027', $updated->name);
    }

    public function test_can_delete_academic_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create();

        $deleted = $this->service->delete($academicYear);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('academic_years', ['id' => $academicYear->id]);
    }

    public function test_can_activate_academic_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        $activated = $this->service->activate($academicYear);

        $this->assertEquals(AcademicYearStatus::Active, $activated->status);
    }

    public function test_activating_new_year_deactivates_previous(): void
    {
        $previousYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $newYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        $this->service->activate($newYear);

        $this->assertDatabaseHas('academic_years', [
            'id' => $previousYear->id,
            'status' => AcademicYearStatus::Draft,
        ]);
        $this->assertDatabaseHas('academic_years', [
            'id' => $newYear->id,
            'status' => AcademicYearStatus::Active,
        ]);
    }

    public function test_can_archive_academic_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $archived = $this->service->archive($academicYear);

        $this->assertEquals(AcademicYearStatus::Archived, $archived->status);
    }

    public function test_cannot_activate_already_active_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $activated = $this->service->activate($academicYear);

        $this->assertEquals(AcademicYearStatus::Active, $activated->status);
    }

    public function test_cannot_archive_non_active_year(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        $archived = $this->service->archive($academicYear);

        $this->assertEquals(AcademicYearStatus::Archived, $archived->status);
    }

    public function test_can_get_active_for_school(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $result = $this->service->getActiveForSchool($this->school->id);

        $this->assertNotNull($result);
        $this->assertEquals($academicYear->id, $result->id);
    }

    public function test_returns_null_when_no_active_year(): void
    {
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        $result = $this->service->getActiveForSchool($this->school->id);

        $this->assertNull($result);
    }

    public function test_can_check_has_active_year(): void
    {
        $this->assertFalse($this->service->hasActiveYear($this->school->id));

        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $this->assertTrue($this->service->hasActiveYear($this->school->id));
    }

    public function test_can_paginate_academic_years(): void
    {
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->count(3)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->count(2)
            ->create();
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $result = $this->service->paginate(['status' => AcademicYearStatus::Active->value], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create(['name' => '2025-2026']);
        AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create(['name' => '2024-2025']);

        $result = $this->service->paginate(['search' => '2026'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([AcademicYearCreated::class]);

        $data = AcademicYearData::fromArray([
            'school_id' => $this->school->id,
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
        ]);

        $this->service->create($data);

        Event::assertDispatched(AcademicYearCreated::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        Event::fake([
            AcademicYearActivated::class,
            AcademicYearStatusChanged::class,
            AcademicYearSwitched::class,
        ]);

        $this->service->activate($academicYear);

        Event::assertDispatched(AcademicYearActivated::class);
        Event::assertDispatched(AcademicYearStatusChanged::class);
        Event::assertDispatched(AcademicYearSwitched::class);
    }

    public function test_status_changed_event_dispatched_on_activate(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        Event::fake([AcademicYearStatusChanged::class]);

        $this->service->activate($academicYear);

        Event::assertDispatched(AcademicYearStatusChanged::class, function ($event) use ($academicYear) {
            return $event->academicYear->id === $academicYear->id
                && $event->oldStatus === AcademicYearStatus::Draft
                && $event->newStatus === AcademicYearStatus::Active;
        });
    }

    public function test_switched_event_dispatched_on_activate(): void
    {
        $previousYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->active()
            ->create();

        $newYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->draft()
            ->create();

        Event::fake([AcademicYearSwitched::class]);

        $this->service->activate($newYear);

        Event::assertDispatched(AcademicYearSwitched::class, function ($event) use ($previousYear, $newYear) {
            return $event->previousYear->id === $previousYear->id
                && $event->newYear->id === $newYear->id
                && $event->school->id === $newYear->school_id;
        });
    }

    public function test_dates_changed_event_dispatched_on_date_update(): void
    {
        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-07-31',
            ]);

        Event::fake([AcademicYearDatesChanged::class]);

        $this->service->update($academicYear, [
            'start_date' => '2025-10-01',
            'end_date' => '2026-08-31',
        ]);

        Event::assertDispatched(AcademicYearDatesChanged::class, function ($event) {
            return $event->oldStartDate === '2025-09-01'
                && $event->oldEndDate === '2026-07-31';
        });
    }

    public function test_cloned_event_dispatched_on_clone(): void
    {
        $source = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create();

        Event::fake([AcademicYearCloned::class]);

        $this->service->clone($source);

        Event::assertDispatched(AcademicYearCloned::class, function ($event) use ($source) {
            return $event->original->id === $source->id;
        });
    }

    public function test_can_clone_academic_year(): void
    {
        $source = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create([
                'name' => '2025-2026',
                'code' => 'AY-2526',
                'start_date' => '2025-09-01',
                'end_date' => '2026-07-31',
            ]);

        $clone = $this->service->clone($source);

        $this->assertNotEquals($source->id, $clone->id);
        $this->assertEquals('2025-2026 (Copy)', $clone->name);
        $this->assertEquals('AY-2526', $clone->code);
        $this->assertEquals(AcademicYearStatus::Draft, $clone->status);
    }

    public function test_clone_preserves_source_fields(): void
    {
        $source = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create([
                'description' => 'Original description',
            ]);

        $clone = $this->service->clone($source);

        $this->assertEquals($source->description, $clone->description);
        $this->assertEquals($source->start_date->toDateString(), $clone->start_date->toDateString());
        $this->assertEquals($source->end_date->toDateString(), $clone->end_date->toDateString());
    }

    public function test_clone_allows_overrides(): void
    {
        $source = AcademicYear::factory()
            ->state(['school_id' => $this->schoolId])
            ->create(['name' => '2025-2026']);

        $clone = $this->service->clone($source, [
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-07-31',
        ]);

        $this->assertEquals('2026-2027', $clone->name);
        $this->assertEquals('2026-09-01', $clone->start_date->toDateString());
        $this->assertEquals('2027-07-31', $clone->end_date->toDateString());
    }
}
