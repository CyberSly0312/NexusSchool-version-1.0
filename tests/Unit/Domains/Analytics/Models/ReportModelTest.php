<?php

namespace Tests\Unit\Domains\Analytics\Models;

use App\Domains\Analytics\Enums\ReportFormat;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use App\Models\Report;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('PRAGMA foreign_keys = OFF');
    }

    public function test_boot_generates_ulid_when_id_is_empty(): void
    {
        $report = Report::factory()->create(['id' => null]);

        $this->assertNotNull($report->id);
        $this->assertEquals(26, strlen($report->id));
    }

    public function test_boot_auto_generates_slug_from_name(): void
    {
        $report = Report::factory()->create(['name' => 'My Report', 'slug' => null]);

        $this->assertEquals('my-report', $report->slug);
    }

    public function test_format_casts_to_report_format_enum(): void
    {
        $report = Report::factory()->create(['format' => 'pdf']);

        $this->assertInstanceOf(ReportFormat::class, $report->format);
        $this->assertEquals(ReportFormat::Pdf, $report->format);
    }

    public function test_status_casts_to_report_status_enum(): void
    {
        $report = Report::factory()->create(['status' => 'draft']);

        $this->assertInstanceOf(ReportStatus::class, $report->status);
        $this->assertEquals(ReportStatus::Draft, $report->status);
    }

    public function test_scheduled_frequency_casts_to_schedule_frequency_enum(): void
    {
        $report = Report::factory()->create(['scheduled_frequency' => 'weekly']);

        $this->assertInstanceOf(ScheduleFrequency::class, $report->scheduled_frequency);
        $this->assertEquals(ScheduleFrequency::Weekly, $report->scheduled_frequency);
    }

    public function test_config_casts_to_array(): void
    {
        $report = Report::factory()->create(['config' => ['columns' => ['name', 'date']]]);

        $this->assertIsArray($report->config);
        $this->assertEquals(['name', 'date'], $report->config['columns']);
    }

    public function test_scheduled_at_casts_to_datetime(): void
    {
        $report = Report::factory()->create(['scheduled_at' => '2026-01-15 10:00:00']);

        $this->assertInstanceOf(Carbon::class, $report->scheduled_at);
    }

    public function test_last_generated_at_casts_to_datetime(): void
    {
        $report = Report::factory()->create(['last_generated_at' => '2026-01-10 08:30:00']);

        $this->assertInstanceOf(Carbon::class, $report->last_generated_at);
    }

    public function test_has_school_relationship(): void
    {
        $report = Report::factory()->create(['school_id' => 'school-1']);

        $this->assertInstanceOf(BelongsTo::class, $report->school());
    }

    public function test_scope_active_excludes_failed(): void
    {
        Report::factory()->create(['status' => 'draft']);
        Report::factory()->create(['status' => 'failed']);

        $active = Report::active()->get();

        $this->assertCount(1, $active);
        $this->assertNotEquals(ReportStatus::Failed, $active->first()->status);
    }

    public function test_scope_for_school_filters_correctly(): void
    {
        Report::factory()->forSchool('school-1')->create();
        Report::factory()->forSchool('school-2')->create();

        $results = Report::forSchool('school-1')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('school-1', $results->first()->school_id);
    }

    public function test_scope_scheduled_filters_correctly(): void
    {
        Report::factory()->create(['status' => 'scheduled', 'scheduled_at' => now()->subDay()]);
        Report::factory()->create(['status' => 'scheduled', 'scheduled_at' => now()->addDay()]);
        Report::factory()->create(['status' => 'draft']);

        $results = Report::scheduled()->get();

        $this->assertCount(1, $results);
    }

    public function test_uses_soft_deletes(): void
    {
        $report = Report::factory()->create();

        $report->delete();

        $this->assertSoftDeleted('reports', ['id' => $report->id]);

        $report->restore();

        $this->assertNotSoftDeleted('reports', ['id' => $report->id]);
    }
}
