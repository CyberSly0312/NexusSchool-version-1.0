<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Workflow;

use App\Domains\Workflow\Services\RuleEngineService;
use App\Models\WorkflowExecution;
use App\Models\WorkflowVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleEngineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RuleEngineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RuleEngineService;
    }

    public function test_evaluate_start_node(): void
    {
        $version = WorkflowVersion::factory()->create([
            'nodes' => [
                ['id' => 'start', 'type' => 'start', 'name' => 'Start'],
                ['id' => 'end', 'type' => 'end', 'name' => 'End'],
            ],
            'edges' => [
                ['id' => 'e1', 'source' => 'start', 'target' => 'end'],
            ],
        ]);

        $execution = WorkflowExecution::factory()->create([
            'workflow_version_id' => $version->id,
            'variables' => [],
            'context' => [],
        ]);

        $result = $this->service->evaluateNode(
            ['id' => 'start', 'type' => 'start', 'name' => 'Start'],
            $execution
        );

        $this->assertEquals('proceed', $result['action']);
        $this->assertEquals('end', $result['next']);
    }

    public function test_evaluate_end_node(): void
    {
        $execution = WorkflowExecution::factory()->create([
            'variables' => [],
            'context' => [],
        ]);

        $result = $this->service->evaluateNode(
            ['id' => 'end', 'type' => 'end', 'name' => 'End'],
            $execution
        );

        $this->assertEquals('complete', $result['action']);
    }

    public function test_find_next_node_id(): void
    {
        $version = WorkflowVersion::factory()->create([
            'nodes' => [
                ['id' => 'start', 'type' => 'start', 'name' => 'Start'],
                ['id' => 'end', 'type' => 'end', 'name' => 'End'],
            ],
            'edges' => [
                ['id' => 'e1', 'source' => 'start', 'target' => 'end'],
            ],
        ]);

        $execution = WorkflowExecution::factory()->create([
            'workflow_version_id' => $version->id,
        ]);

        $next = $this->service->findNextNodeId('start', $execution);

        $this->assertEquals('end', $next);
    }
}
