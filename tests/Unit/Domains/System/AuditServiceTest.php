<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\DTOs\AuditLogData;
use App\Domains\System\Services\AuditService;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuditService;
    }

    public function test_record_audit(): void
    {
        $result = $this->service->record([
            'auditable_type' => 'App\\Models\\User',
            'auditable_id' => fake()->uuid(),
            'action' => 'created',
            'description' => 'User created',
        ]);

        $this->assertInstanceOf(AuditLogData::class, $result);
        $this->assertEquals('created', $result->action);
    }

    public function test_paginate_audits(): void
    {
        AuditLog::factory()->count(5)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }
}
