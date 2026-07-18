<?php

namespace Tests\Unit\Domains\Analytics\Models;

use App\Domains\Analytics\Enums\KpiStatus;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KpiModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('PRAGMA foreign_keys = OFF');
    }

    public function test_boot_generates_ulid_when_id_is_empty(): void
    {
        $kpi = Kpi::factory()->create(['id' => null]);

        $this->assertNotNull($kpi->id);
        $this->assertEquals(26, strlen($kpi->id));
    }

    public function test_boot_auto_generates_slug_from_name(): void
    {
        $kpi = Kpi::factory()->create(['name' => 'My KPI Metric', 'slug' => null]);

        $this->assertEquals('my-kpi-metric', $kpi->slug);
    }

    public function test_status_casts_to_kpi_status_enum(): void
    {
        $kpi = Kpi::factory()->create(['status' => 'active']);

        $this->assertInstanceOf(KpiStatus::class, $kpi->status);
        $this->assertEquals(KpiStatus::Active, $kpi->status);
    }

    public function test_formula_casts_to_array(): void
    {
        $kpi = Kpi::factory()->create(['formula' => ['operator' => 'sum', 'field' => 'enrollment']]);

        $this->assertIsArray($kpi->formula);
        $this->assertEquals('sum', $kpi->formula['operator']);
    }

    public function test_target_value_is_decimal_2(): void
    {
        $kpi = Kpi::factory()->create(['target_value' => 95.5]);

        $this->assertEquals('95.50', $kpi->target_value);
    }

    public function test_cached_value_is_decimal_2(): void
    {
        $kpi = Kpi::factory()->create(['cached_value' => 82.3]);

        $this->assertEquals('82.30', $kpi->cached_value);
    }

    public function test_has_school_relationship(): void
    {
        $kpi = Kpi::factory()->create(['school_id' => 'school-1']);

        $this->assertInstanceOf(BelongsTo::class, $kpi->school());
    }

    public function test_scope_active_filters_correctly(): void
    {
        Kpi::factory()->create(['status' => 'active']);
        Kpi::factory()->create(['status' => 'inactive']);

        $active = Kpi::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals(KpiStatus::Active, $active->first()->status);
    }

    public function test_scope_for_school_filters_correctly(): void
    {
        Kpi::factory()->forSchool('school-1')->create();
        Kpi::factory()->forSchool('school-2')->create();

        $results = Kpi::forSchool('school-1')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('school-1', $results->first()->school_id);
    }

    public function test_scope_by_category_filters_correctly(): void
    {
        Kpi::factory()->create(['category' => 'enrollment']);
        Kpi::factory()->create(['category' => 'attendance']);

        $results = Kpi::byCategory('enrollment')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('enrollment', $results->first()->category);
    }

    public function test_is_cache_valid_returns_true_when_cache_is_fresh(): void
    {
        $kpi = Kpi::factory()->create([
            'cached_value' => 75.5,
            'cached_at' => now()->subMinutes(30),
        ]);

        $this->assertTrue($kpi->isCacheValid());
    }

    public function test_is_cache_valid_returns_false_when_cached_at_is_expired(): void
    {
        $kpi = Kpi::factory()->create([
            'cached_value' => 75.5,
            'cached_at' => now()->subMinutes(90),
        ]);

        $this->assertFalse($kpi->isCacheValid());
    }

    public function test_is_cache_valid_returns_false_when_cached_value_is_null(): void
    {
        $kpi = Kpi::factory()->create([
            'cached_value' => null,
            'cached_at' => now(),
        ]);

        $this->assertFalse($kpi->isCacheValid());
    }

    public function test_uses_soft_deletes(): void
    {
        $kpi = Kpi::factory()->create();

        $kpi->delete();

        $this->assertSoftDeleted('kpis', ['id' => $kpi->id]);

        $kpi->restore();

        $this->assertNotSoftDeleted('kpis', ['id' => $kpi->id]);
    }
}
