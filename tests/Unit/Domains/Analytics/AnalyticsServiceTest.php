<?php

namespace Tests\Unit\Domains\Analytics;

use App\Domains\Analytics\Services\AnalyticsService;
use App\Models\AnalyticsSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AnalyticsService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_snapshot(): void
    {
        $data = ['total' => 100, 'active' => 90];
        $result = $this->service->createSnapshot($this->schoolId, 'enrollment', $data);

        $this->assertEquals('enrollment', $result->snapshotType);
        $this->assertEquals($data, $result->data);
        $this->assertTrue($result->isImmutable);
    }

    public function test_can_get_snapshots(): void
    {
        AnalyticsSnapshot::factory()->forSchool($this->schoolId)->enrollment()->create();
        AnalyticsSnapshot::factory()->forSchool($this->schoolId)->attendance()->create();

        $snapshots = $this->service->getSnapshots($this->schoolId);
        $this->assertCount(2, $snapshots);
    }

    public function test_can_filter_snapshots_by_type(): void
    {
        AnalyticsSnapshot::factory()->forSchool($this->schoolId)->enrollment()->create();
        AnalyticsSnapshot::factory()->forSchool($this->schoolId)->attendance()->create();

        $snapshots = $this->service->getSnapshots($this->schoolId, 'enrollment');
        $this->assertCount(1, $snapshots);
    }

    public function test_get_enrollment_stats_returns_array(): void
    {
        $result = $this->service->getEnrollmentStats($this->schoolId);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('active', $result);
    }

    public function test_create_snapshot_with_period_params(): void
    {
        $result = $this->service->createSnapshot(
            $this->schoolId,
            'enrollment',
            ['total' => 50],
            '2024-01-01',
            '2024-01-31'
        );

        $this->assertNotNull($result->periodStart);
        $this->assertNotNull($result->periodEnd);
    }

    public function test_get_snapshots_filters_by_period_start(): void
    {
        $this->service->createSnapshot($this->schoolId, 'enrollment', ['a' => 1], '2024-01-01', '2024-01-31');
        $this->service->createSnapshot($this->schoolId, 'enrollment', ['b' => 2], '2024-06-01', '2024-06-30');

        $snapshots = $this->service->getSnapshots($this->schoolId, null, '2024-06-01');
        $this->assertCount(1, $snapshots);
    }

    public function test_get_snapshots_ordered_by_generated_at_desc(): void
    {
        $this->service->createSnapshot($this->schoolId, 'enrollment', ['a' => 1]);
        $this->service->createSnapshot($this->schoolId, 'enrollment', ['b' => 2]);

        $snapshots = $this->service->getSnapshots($this->schoolId);
        $this->assertTrue($snapshots->first()->generated_at->gte($snapshots->last()->generated_at));
    }
}
