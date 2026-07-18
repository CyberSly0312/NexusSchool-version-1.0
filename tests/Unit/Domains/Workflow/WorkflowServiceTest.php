<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Workflow;

use App\Domains\Workflow\DTOs\WorkflowData;
use App\Domains\Workflow\Services\WorkflowService;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WorkflowService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WorkflowService;
    }

    public function test_create_workflow(): void
    {
        $data = [
            'school_id' => fake()->uuid(),
            'name' => 'Student Admission Approval',
            'description' => 'Workflow for student admission',
            'category' => 'approval',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(WorkflowData::class, $result);
        $this->assertEquals('Student Admission Approval', $result->name);
        $this->assertEquals('student-admission-approval', $result->slug);
        $this->assertEquals('draft', $result->status);
        $this->assertTrue($result->isActive);
    }

    public function test_create_workflow_generates_slug(): void
    {
        $data = [
            'school_id' => fake()->uuid(),
            'name' => 'Test Workflow Name',
        ];

        $result = $this->service->create($data);

        $this->assertEquals('test-workflow-name', $result->slug);
    }

    public function test_update_workflow(): void
    {
        $workflow = Workflow::factory()->create([
            'name' => 'Original Name',
        ]);

        $result = $this->service->update($workflow, [
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('updated-name', $result->slug);
    }

    public function test_find_by_id(): void
    {
        $workflow = Workflow::factory()->create();

        $result = $this->service->findById($workflow->id);

        $this->assertEquals($workflow->id, $result->id);
    }

    public function test_paginate_workflows(): void
    {
        Workflow::factory()->count(5)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }
}
