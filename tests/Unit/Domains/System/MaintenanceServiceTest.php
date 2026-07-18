<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\DTOs\MaintenanceWindowData;
use App\Domains\System\Services\MaintenanceService;
use App\Models\MaintenanceWindow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MaintenanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MaintenanceService;
    }

    public function test_create_maintenance_window(): void
    {
        $result = $this->service->create([
            'name' => 'Test Window',
            'starts_at' => now()->addDay()->toIso8601String(),
            'ends_at' => now()->addDay()->addHours(4)->toIso8601String(),
        ]);

        $this->assertInstanceOf(MaintenanceWindowData::class, $result);
        $this->assertEquals('Test Window', $result->name);
        $this->assertEquals('pending', $result->status);
    }

    public function test_start_and_end_window(): void
    {
        $window = MaintenanceWindow::factory()->create(['status' => 'pending']);

        $result = $this->service->start($window);
        $this->assertEquals('active', $result->status);

        $result = $this->service->end($window->fresh());
        $this->assertEquals('completed', $result->status);
    }

    public function test_is_active(): void
    {
        MaintenanceWindow::factory()->active()->create();

        $this->assertTrue($this->service->isActive());
    }
}
