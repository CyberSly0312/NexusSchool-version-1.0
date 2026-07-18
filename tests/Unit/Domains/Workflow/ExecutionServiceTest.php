<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Workflow;

use App\Domains\Workflow\Services\ExecutionService;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Models\WorkflowVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExecutionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExecutionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecutionService;
    }

    public function test_start_execution(): void
    {
        $workflow = Workflow::factory()->active()->create();
        WorkflowVersion::factory()->published()->create([
            'workflow_id' => $workflow->id,
            'school_id' => $workflow->school_id,
        ]);

        $result = $this->service->startExecution([
            'school_id' => $workflow->school_id,
            'workflow_id' => $workflow->id,
        ]);

        $this->assertEquals('running', $result->status);
        $this->assertNotNull($result->startedAt);
    }

    public function test_retry_execution(): void
    {
        $execution = WorkflowExecution::factory()->failed()->create([
            'retry_count' => 0,
            'max_retries' => 3,
        ]);

        $result = $this->service->retryExecution($execution);

        $this->assertEquals('pending', $result->status);
        $this->assertEquals(1, $result->retryCount);
        $this->assertNull($result->error);
    }

    public function test_cancel_execution(): void
    {
        $execution = WorkflowExecution::factory()->running()->create();

        $result = $this->service->cancelExecution($execution);

        $this->assertEquals('cancelled', $result->status);
    }
}
