<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateAnalyticsSnapshot;
use App\Models\AnalyticsSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CreateAnalyticsSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_creates_enrollment_snapshot(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'enrollment');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('enrollment', $snapshot->snapshot_type);
        $this->assertEquals('1', $snapshot->school_id);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_handle_creates_attendance_snapshot(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'attendance');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('attendance', $snapshot->snapshot_type);
        $this->assertEquals('1', $snapshot->school_id);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_handle_creates_library_snapshot(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'library');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('library', $snapshot->snapshot_type);
        $this->assertEquals('1', $snapshot->school_id);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_handle_creates_transport_snapshot(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'transport');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('transport', $snapshot->snapshot_type);
        $this->assertEquals('1', $snapshot->school_id);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_handle_creates_communication_snapshot(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'communication');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('communication', $snapshot->snapshot_type);
        $this->assertEquals('1', $snapshot->school_id);
        $this->assertTrue($snapshot->is_immutable);
    }

    public function test_handle_creates_empty_snapshot_for_unknown_type(): void
    {
        CreateAnalyticsSnapshot::dispatchSync('1', 'unknown');

        $this->assertDatabaseCount('analytics_snapshots', 1);

        $snapshot = AnalyticsSnapshot::first();
        $this->assertEquals('unknown', $snapshot->snapshot_type);
        $this->assertEquals([], $snapshot->data);
    }

    public function test_job_has_correct_try_count(): void
    {
        $job = new CreateAnalyticsSnapshot('some-id', 'enrollment');

        $this->assertEquals(2, $job->tries);
    }

    public function test_job_has_correct_timeout(): void
    {
        $job = new CreateAnalyticsSnapshot('some-id', 'enrollment');

        $this->assertEquals(300, $job->timeout);
    }

    public function test_failed_logs_error(): void
    {
        Log::shouldReceive('error')
            ->once();

        $job = new CreateAnalyticsSnapshot('some-id', 'enrollment');
        $job->failed(new \Exception('Something went wrong'));
    }
}
