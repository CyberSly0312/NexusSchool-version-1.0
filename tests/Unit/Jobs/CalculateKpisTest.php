<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CalculateKpis;
use App\Models\Kpi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CalculateKpisTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_calculates_all_active_kpis_for_school(): void
    {
        Kpi::factory()->count(3)->active()->create(['school_id' => '1']);
        Kpi::factory()->archived()->create(['school_id' => '1']);

        Log::shouldReceive('info')
            ->atLeast()
            ->once();
        Log::shouldReceive('error')
            ->zeroOrMoreTimes();

        CalculateKpis::dispatchSync('1');
    }

    public function test_job_has_correct_try_count(): void
    {
        $job = new CalculateKpis('some-id');

        $this->assertEquals(2, $job->tries);
    }

    public function test_job_has_correct_timeout(): void
    {
        $job = new CalculateKpis('some-id');

        $this->assertEquals(600, $job->timeout);
    }

    public function test_failed_logs_error(): void
    {
        Log::shouldReceive('error')
            ->once();

        $job = new CalculateKpis('some-id');
        $job->failed(new \Exception('Something went wrong'));
    }
}
