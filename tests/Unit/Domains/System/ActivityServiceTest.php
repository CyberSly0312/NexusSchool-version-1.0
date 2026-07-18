<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\Services\ActivityService;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ActivityService;
    }

    public function test_record_activity(): void
    {
        $result = $this->service->record([
            'event' => 'created',
            'description' => 'Activity recorded',
        ]);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals('created', $result->event);
    }

    public function test_paginate_activities(): void
    {
        ActivityLog::factory()->count(5)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }
}
