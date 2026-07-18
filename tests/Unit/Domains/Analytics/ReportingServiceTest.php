<?php

namespace Tests\Unit\Domains\Analytics;

use App\Domains\Analytics\DTOs\ReportData;
use App\Domains\Analytics\Events\ReportGenerated;
use App\Domains\Analytics\Events\ReportScheduled;
use App\Domains\Analytics\Services\ReportingService;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportingService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReportingService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_report(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test Report',
            'type' => 'enrollment',
            'format' => 'pdf',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(ReportData::class, $result);
        $this->assertEquals('Test Report', $result->name);
        $this->assertEquals('pdf', $result->format);
    }

    public function test_can_update_report(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create(['name' => 'Old Report']);
        $result = $this->service->update($report, ['name' => 'New Report']);
        $this->assertEquals('New Report', $result->name);
    }

    public function test_can_delete_report(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($report);
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    public function test_can_schedule_report(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->draft()->create();
        $result = $this->service->schedule($report, 'weekly');
        $this->assertEquals('scheduled', $result->status);
        $this->assertEquals('weekly', $result->scheduledFrequency);
    }

    public function test_can_mark_completed(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create(['status' => 'generating']);
        $result = $this->service->markCompleted($report);
        $this->assertEquals('completed', $result->status);
        $this->assertNotNull($result->lastGeneratedAt);
    }

    public function test_can_mark_failed(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create(['status' => 'generating']);
        $result = $this->service->markFailed($report);
        $this->assertEquals('failed', $result->status);
    }

    public function test_can_get_scheduled_reports(): void
    {
        Report::factory()->forSchool($this->schoolId)->scheduled()->create();
        Report::factory()->forSchool($this->schoolId)->draft()->create();
        $reports = $this->service->getScheduledReports();
        $this->assertCount(1, $reports);
    }

    public function test_can_find_by_id(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($report->id);
        $this->assertEquals($report->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->findById('non-existent-id');
    }

    public function test_can_paginate_reports(): void
    {
        Report::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_events_dispatched_on_generate(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create(['status' => 'generating']);
        Event::fake([ReportGenerated::class]);
        $this->service->markCompleted($report);
        Event::assertDispatched(ReportGenerated::class);
    }

    public function test_can_mark_generating(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->draft()->create();
        $result = $this->service->markGenerating($report);
        $this->assertEquals('generating', $result->status);
    }

    public function test_schedule_rejects_generating_report(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->create(['status' => 'generating']);
        $this->expectException(ValidationException::class);
        $this->service->schedule($report, 'weekly');
    }

    public function test_schedule_dispatches_report_scheduled_event(): void
    {
        $report = Report::factory()->forSchool($this->schoolId)->draft()->create();
        Event::fake([ReportScheduled::class]);
        $this->service->schedule($report, 'weekly');
        Event::assertDispatched(ReportScheduled::class);
    }

    public function test_create_rejects_duplicate_name(): void
    {
        Report::factory()->forSchool($this->schoolId)->create(['name' => 'Duplicate Report']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Duplicate Report',
            'type' => 'enrollment',
            'format' => 'pdf',
        ]);
    }
}
