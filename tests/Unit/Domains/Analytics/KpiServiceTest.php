<?php

namespace Tests\Unit\Domains\Analytics;

use App\Domains\Analytics\DTOs\KpiData;
use App\Domains\Analytics\Events\KpiRecalculated;
use App\Domains\Analytics\Services\KpiService;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KpiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KpiService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KpiService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_kpi(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test KPI',
            'category' => 'enrollment',
            'target_value' => 100,
            'unit' => 'students',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(KpiData::class, $result);
        $this->assertEquals('Test KPI', $result->name);
        $this->assertEquals('enrollment', $result->category);
    }

    public function test_can_update_kpi(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create(['name' => 'Old KPI']);
        $result = $this->service->update($kpi, ['name' => 'New KPI']);
        $this->assertEquals('New KPI', $result->name);
    }

    public function test_can_delete_kpi(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($kpi);
        $this->assertSoftDeleted('kpis', ['id' => $kpi->id]);
    }

    public function test_can_archive_kpi(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->active()->create();
        $result = $this->service->archive($kpi);
        $this->assertEquals('archived', $result->status);
    }

    public function test_can_activate_kpi(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->archived()->create();
        $result = $this->service->activate($kpi);
        $this->assertEquals('active', $result->status);
    }

    public function test_can_calculate_kpi_with_value(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->calculate($kpi, 42.5);
        $this->assertEquals(42.5, $result->cachedValue);
        $this->assertNotNull($result->lastCalculatedAt);
    }

    public function test_calculate_dispatches_event(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create();
        Event::fake([KpiRecalculated::class]);
        $this->service->calculate($kpi, 10.0);
        Event::assertDispatched(KpiRecalculated::class);
    }

    public function test_can_find_by_id(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($kpi->id);
        $this->assertEquals($kpi->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->findById('non-existent-id');
    }

    public function test_can_paginate_kpis(): void
    {
        Kpi::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_duplicate_kpi_name_is_rejected(): void
    {
        Kpi::factory()->forSchool($this->schoolId)->create(['name' => 'Existing KPI']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing KPI',
            'category' => 'enrollment',
        ]);
    }

    public function test_calculate_without_value_returns_zero(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create(['formula' => null]);
        $result = $this->service->calculate($kpi);
        $this->assertEquals(0.0, $result->cachedValue);
    }

    public function test_calculate_all_for_school_calculates_active_kpis(): void
    {
        Kpi::factory()->forSchool($this->schoolId)->active()->create(['name' => 'Active 1']);
        Kpi::factory()->forSchool($this->schoolId)->active()->create(['name' => 'Active 2']);
        Kpi::factory()->forSchool($this->schoolId)->archived()->create(['name' => 'Archived']);

        $count = $this->service->calculateAllForSchool($this->schoolId);
        $this->assertEquals(2, $count);
    }

    public function test_get_cached_value_returns_value_when_valid(): void
    {
        $kpi = Kpi::factory()->forSchool($this->schoolId)->create();
        $this->service->calculate($kpi, 42.5);

        $cached = $this->service->getCachedValue($kpi->fresh());
        $this->assertEquals(42.5, $cached);
    }

    public function test_paginate_filters_by_search_term(): void
    {
        Kpi::factory()->forSchool($this->schoolId)->create(['name' => 'Retention Rate']);
        Kpi::factory()->forSchool($this->schoolId)->create(['name' => 'Enrollment Growth']);

        $result = $this->service->paginate(['search' => 'Retention'], 10);
        $this->assertEquals(1, $result->total());
    }
}
