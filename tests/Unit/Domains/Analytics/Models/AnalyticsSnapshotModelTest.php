<?php

namespace Tests\Unit\Domains\Analytics\Models;

use App\Models\AnalyticsSnapshot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnalyticsSnapshotModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('PRAGMA foreign_keys = OFF');
    }

    public function test_boot_generates_ulid_when_id_is_empty(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['id' => null]);

        $this->assertNotNull($snapshot->id);
        $this->assertEquals(26, strlen($snapshot->id));
    }

    public function test_data_casts_to_array(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['data' => ['total' => 100, 'active' => 50]]);

        $this->assertIsArray($snapshot->data);
        $this->assertEquals(100, $snapshot->data['total']);
    }

    public function test_period_start_casts_to_datetime(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['period_start' => '2026-01-01 00:00:00']);

        $this->assertInstanceOf(Carbon::class, $snapshot->period_start);
    }

    public function test_period_end_casts_to_datetime(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['period_end' => '2026-01-31 23:59:59']);

        $this->assertInstanceOf(Carbon::class, $snapshot->period_end);
    }

    public function test_generated_at_casts_to_datetime(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['generated_at' => '2026-01-15 12:00:00']);

        $this->assertInstanceOf(Carbon::class, $snapshot->generated_at);
    }

    public function test_is_immutable_casts_to_boolean(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['is_immutable' => true]);

        $this->assertIsBool($snapshot->is_immutable);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_has_school_relationship(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create(['school_id' => 'school-1']);

        $this->assertInstanceOf(BelongsTo::class, $snapshot->school());
    }

    public function test_scope_for_school_filters_correctly(): void
    {
        AnalyticsSnapshot::factory()->forSchool('school-1')->create();
        AnalyticsSnapshot::factory()->forSchool('school-2')->create();

        $results = AnalyticsSnapshot::forSchool('school-1')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('school-1', $results->first()->school_id);
    }

    public function test_scope_by_type_filters_correctly(): void
    {
        AnalyticsSnapshot::factory()->create(['snapshot_type' => 'enrollment']);
        AnalyticsSnapshot::factory()->create(['snapshot_type' => 'attendance']);

        $results = AnalyticsSnapshot::byType('enrollment')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('enrollment', $results->first()->snapshot_type);
    }

    public function test_scope_for_period_filters_correctly(): void
    {
        AnalyticsSnapshot::factory()->create([
            'period_start' => '2026-01-01',
            'period_end' => '2026-01-31',
        ]);
        AnalyticsSnapshot::factory()->create([
            'period_start' => '2026-06-01',
            'period_end' => '2026-06-30',
        ]);

        $results = AnalyticsSnapshot::forPeriod('2026-01-01', '2026-01-31')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('2026-01-01', $results->first()->period_start->format('Y-m-d'));
    }

    public function test_scope_immutable_filters_correctly(): void
    {
        AnalyticsSnapshot::factory()->create(['is_immutable' => true]);
        AnalyticsSnapshot::factory()->create(['is_immutable' => false]);

        $immutable = AnalyticsSnapshot::immutable()->get();

        $this->assertCount(1, $immutable);
        $this->assertTrue($immutable->first()->is_immutable);
    }

    public function test_does_not_use_soft_deletes(): void
    {
        $snapshot = AnalyticsSnapshot::factory()->create();

        $snapshot->delete();

        $this->assertDatabaseMissing('analytics_snapshots', ['id' => $snapshot->id]);
    }
}
