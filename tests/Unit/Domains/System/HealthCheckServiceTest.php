<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\Services\HealthCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    protected HealthCheckService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HealthCheckService;
    }

    public function test_run_all_checks(): void
    {
        $result = $this->service->runAllChecks();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_get_status(): void
    {
        $status = $this->service->getStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('overall', $status);
        $this->assertContains($status['overall'], ['healthy', 'degraded', 'unhealthy', 'unknown']);
    }
}
