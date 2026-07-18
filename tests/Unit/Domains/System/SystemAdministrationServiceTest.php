<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\System;

use App\Domains\System\DTOs\SystemSettingData;
use App\Domains\System\Services\SystemAdministrationService;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemAdministrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SystemAdministrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SystemAdministrationService;
    }

    public function test_create_setting(): void
    {
        $result = $this->service->create([
            'group' => 'general',
            'key' => 'school_name',
            'value' => 'Nexus School',
            'type' => 'string',
        ]);

        $this->assertInstanceOf(SystemSettingData::class, $result);
        $this->assertEquals('general', $result->group);
        $this->assertEquals('school_name', $result->key);
    }

    public function test_get_setting(): void
    {
        $this->service->create([
            'group' => 'general',
            'key' => 'school_name',
            'value' => 'Nexus School',
        ]);

        $value = $this->service->get('general', 'school_name');

        $this->assertEquals('Nexus School', $value);
    }

    public function test_update_setting(): void
    {
        $setting = SystemSetting::factory()->create([
            'group' => 'general',
            'key' => 'school_name',
        ]);

        $result = $this->service->update($setting, ['value' => 'Updated Name']);

        $this->assertEquals('Updated Name', $result->value);
    }

    public function test_cannot_update_readonly(): void
    {
        $setting = SystemSetting::factory()->create(['is_readonly' => true]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->service->update($setting, ['value' => 'New Value']);
    }

    public function test_delete_setting(): void
    {
        $setting = SystemSetting::factory()->create();

        $this->service->delete($setting);

        $this->assertSoftDeleted('system_settings', ['id' => $setting->id]);
    }
}
