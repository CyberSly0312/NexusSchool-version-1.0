<?php

namespace Tests\Unit\Domains\AcademicTerm;

use App\Domains\AcademicTerm\DTOs\AcademicTermData;
use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use App\Domains\AcademicTerm\Events\AcademicTermActivated;
use App\Domains\AcademicTerm\Events\AcademicTermArchived;
use App\Domains\AcademicTerm\Events\AcademicTermCreated;
use App\Domains\AcademicTerm\Events\AcademicTermStatusChanged;
use App\Domains\AcademicTerm\Services\AcademicTermService;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AcademicTermServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicTermService $service;

    protected string $schoolId;

    protected AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AcademicTermService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->schoolId = $schoolId;

        $this->academicYear = AcademicYear::factory()
            ->state(['school_id' => $schoolId])
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-07-31',
            ]);
    }

    public function test_can_create_academic_term(): void
    {
        $data = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-31',
            'order' => 1,
        ]);

        $term = $this->service->create($data);

        $this->assertInstanceOf(AcademicTerm::class, $term);
        $this->assertEquals('Semester 1', $term->name);
        $this->assertEquals('S1', $term->code);
        $this->assertEquals(AcademicTermStatus::Draft, $term->status);
        $this->assertEquals(1, $term->order);
    }

    public function test_can_update_academic_term(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create(['name' => 'Semester 1']);

        $updated = $this->service->update($term, ['name' => 'Term 1']);

        $this->assertEquals('Term 1', $updated->name);
    }

    public function test_can_delete_academic_term(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create();

        $deleted = $this->service->delete($term);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('academic_terms', ['id' => $term->id]);
    }

    public function test_can_activate_academic_term(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->create();

        $activated = $this->service->activate($term);

        $this->assertEquals(AcademicTermStatus::Active, $activated->status);
    }

    public function test_activating_new_term_deactivates_previous(): void
    {
        $previousTerm = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        $newTerm = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->create(['start_date' => '2026-02-01', 'end_date' => '2026-07-31']);

        $this->service->activate($newTerm);

        $this->assertDatabaseHas('academic_terms', [
            'id' => $previousTerm->id,
            'status' => AcademicTermStatus::Draft,
        ]);
        $this->assertDatabaseHas('academic_terms', [
            'id' => $newTerm->id,
            'status' => AcademicTermStatus::Active,
        ]);
    }

    public function test_can_archive_academic_term(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        $archived = $this->service->archive($term);

        $this->assertEquals(AcademicTermStatus::Archived, $archived->status);
    }

    public function test_can_get_active_for_year(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        $result = $this->service->getActiveForYear($this->academicYear->id);

        $this->assertNotNull($result);
        $this->assertEquals($term->id, $result->id);
    }

    public function test_returns_null_when_no_active_term(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->create();

        $result = $this->service->getActiveForYear($this->academicYear->id);

        $this->assertNull($result);
    }

    public function test_can_check_has_active_term(): void
    {
        $this->assertFalse($this->service->hasActiveTerm($this->academicYear->id));

        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        $this->assertTrue($this->service->hasActiveTerm($this->academicYear->id));
    }

    public function test_can_paginate_academic_terms(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->count(3)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->count(2)
            ->create();
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        $result = $this->service->paginate(['status' => AcademicTermStatus::Active->value], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create(['name' => 'Semester 1']);
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create(['name' => 'Semester 2']);

        $result = $this->service->paginate(['search' => 'Semester 1'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_term_dates_must_be_within_academic_year(): void
    {
        $data = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Invalid Term',
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-30',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_term_end_date_after_year_end_is_invalid(): void
    {
        $data = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Invalid Term',
            'start_date' => '2026-06-01',
            'end_date' => '2026-12-31',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_overlapping_terms_are_rejected(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]);

        $overlappingData = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Overlapping Term',
            'start_date' => '2025-12-01',
            'end_date' => '2026-03-31',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($overlappingData);
    }

    public function test_non_overlapping_terms_are_allowed(): void
    {
        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]);

        $data = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Semester 2',
            'start_date' => '2026-02-01',
            'end_date' => '2026-07-31',
        ]);

        $term = $this->service->create($data);

        $this->assertNotNull($term);
        $this->assertEquals('Semester 2', $term->name);
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([AcademicTermCreated::class]);

        $data = AcademicTermData::fromArray([
            'academic_year_id' => $this->academicYear->id,
            'school_id' => $this->schoolId,
            'name' => 'Semester 1',
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-31',
        ]);

        $this->service->create($data);

        Event::assertDispatched(AcademicTermCreated::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->create();

        Event::fake([
            AcademicTermActivated::class,
            AcademicTermStatusChanged::class,
        ]);

        $this->service->activate($term);

        Event::assertDispatched(AcademicTermActivated::class);
        Event::assertDispatched(AcademicTermStatusChanged::class);
    }

    public function test_status_changed_event_dispatched_on_activate(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->draft()
            ->create();

        Event::fake([AcademicTermStatusChanged::class]);

        $this->service->activate($term);

        Event::assertDispatched(AcademicTermStatusChanged::class, function ($event) use ($term) {
            return $event->academicTerm->id === $term->id
                && $event->oldStatus === AcademicTermStatus::Draft
                && $event->newStatus === AcademicTermStatus::Active;
        });
    }

    public function test_events_dispatched_on_archive(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->active()
            ->create();

        Event::fake([
            AcademicTermArchived::class,
            AcademicTermStatusChanged::class,
        ]);

        $this->service->archive($term);

        Event::assertDispatched(AcademicTermArchived::class);
        Event::assertDispatched(AcademicTermStatusChanged::class);
    }

    public function test_can_filter_by_academic_year(): void
    {
        $uniqueName = '2099-2100';
        $otherYear = AcademicYear::factory()
            ->state([
                'school_id' => $this->schoolId,
                'name' => $uniqueName,
                'code' => 'AY-2099-2100',
                'start_date' => '2099-09-01',
                'end_date' => '2100-07-31',
            ])
            ->create();

        AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create();

        AcademicTerm::factory()
            ->forAcademicYear($otherYear)
            ->create();

        $result = $this->service->paginate(['academic_year_id' => $this->academicYear->id], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_update_validates_date_containment(): void
    {
        $term = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]);

        $this->expectException(ValidationException::class);

        $this->service->update($term, [
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-30',
        ]);
    }

    public function test_update_validates_no_overlap(): void
    {
        $term1 = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create([
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]);

        $term2 = AcademicTerm::factory()
            ->forAcademicYear($this->academicYear)
            ->create([
                'start_date' => '2026-02-01',
                'end_date' => '2026-07-31',
            ]);

        $this->expectException(ValidationException::class);

        $this->service->update($term2, [
            'start_date' => '2025-12-01',
            'end_date' => '2026-03-31',
        ]);
    }
}
