<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\DTOs\BackupData;
use App\Domains\System\Services\BackupService;
use App\Models\Backup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BackupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BackupService;
    }

    public function test_create_backup(): void
    {
        $result = $this->service->create([
            'name' => 'Test Backup',
            'type' => 'manual',
        ]);

        $this->assertInstanceOf(BackupData::class, $result);
        $this->assertEquals('Test Backup', $result->name);
        $this->assertEquals('pending', $result->status);
    }

    public function test_paginate_backups(): void
    {
        Backup::factory()->count(3)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(3, $result->total());
    }
}
