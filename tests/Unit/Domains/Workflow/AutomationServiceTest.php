<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Workflow;

use App\Domains\Workflow\DTOs\AutomationRuleData;
use App\Domains\Workflow\Services\AutomationService;
use App\Models\AutomationRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AutomationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AutomationService;
    }

    public function test_create_automation_rule(): void
    {
        $data = [
            'school_id' => fake()->uuid(),
            'name' => 'Fee Reminder',
            'trigger_type' => 'event',
            'event_name' => 'fee.overdue',
            'actions' => [['type' => 'notify', 'parameters' => ['channel' => 'email']]],
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(AutomationRuleData::class, $result);
        $this->assertEquals('Fee Reminder', $result->name);
        $this->assertTrue($result->isActive);
    }

    public function test_enable_disable_rule(): void
    {
        $rule = AutomationRule::factory()->active()->create();

        $result = $this->service->disable($rule);
        $this->assertFalse($result->isActive);

        $result = $this->service->enable($rule);
        $this->assertTrue($result->isActive);
    }

    public function test_paginate_rules(): void
    {
        AutomationRule::factory()->count(3)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }
}
