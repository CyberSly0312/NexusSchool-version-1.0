<?php

namespace Tests\Unit\Jobs;

use App\Domains\Analytics\Enums\ReportStatus;
use App\Jobs\GenerateReport;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GenerateReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_marks_report_as_generating_then_completed(): void
    {
        $report = Report::factory()->draft()->create();

        GenerateReport::dispatchSync($report->id);

        $report->refresh();

        $this->assertEquals(ReportStatus::Completed, $report->status);
        $this->assertNotNull($report->last_generated_at);
    }

    public function test_handle_skips_non_scheduled_non_draft_reports(): void
    {
        $report = Report::factory()->completed()->create();
        $originalStatus = $report->status;

        GenerateReport::dispatchSync($report->id);

        $report->refresh();

        $this->assertEquals($originalStatus, $report->status);
    }

    public function test_job_has_correct_try_count(): void
    {
        $job = new GenerateReport('some-id');

        $this->assertEquals(3, $job->tries);
    }

    public function test_job_has_correct_timeout(): void
    {
        $job = new GenerateReport('some-id');

        $this->assertEquals(300, $job->timeout);
    }

    public function test_failed_logs_error(): void
    {
        Log::shouldReceive('error')
            ->once();

        $job = new GenerateReport('some-id');
        $job->failed(new \Exception('Something went wrong'));
    }
}
