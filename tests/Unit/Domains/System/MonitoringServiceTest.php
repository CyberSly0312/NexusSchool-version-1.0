<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\Services\MonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MonitoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MonitoringService;
    }

    public function test_check_database(): void
    {
        $result = $this->service->checkDatabase();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('healthy', $result['status']);
    }

    public function test_check_storage(): void
    {
        $result = $this->service->checkStorage();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
    }
}
