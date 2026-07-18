<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\System;

use App\Filament\Resources\SystemSettingResource;
use App\Filament\Resources\SystemSettingResource\Pages\CreateSystemSetting;
use App\Filament\Resources\SystemSettingResource\Pages\EditSystemSetting;
use App\Models\SystemSetting;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class SystemSettingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = (string) Str::ulid();
        DB::table('tenants')->insert([
            'id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school',
            'database' => 'test-school-db',
            'domain' => 'test.nexus.school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_list_system_settings(): void
    {
        SystemSetting::factory()->count(3)->create();

        $this->get(SystemSettingResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_form(): void
    {
        $this->get(SystemSettingResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_form(): void
    {
        $setting = SystemSetting::factory()->create();

        $this->get(SystemSettingResource::getUrl('edit', ['record' => $setting]))
            ->assertSuccessful();
    }

    public function test_can_create_system_setting(): void
    {
        Livewire::test(CreateSystemSetting::class)
            ->fillForm([
                'group' => 'general',
                'key' => 'site-name',
                'value' => 'My School',
                'type' => 'string',
                'description' => 'The name of the school',
            ])
            ->call('create');

        $this->assertDatabaseHas('system_settings', [
            'group' => 'general',
            'key' => 'site-name',
            'value' => 'My School',
        ]);
    }

    public function test_can_update_system_setting(): void
    {
        $setting = SystemSetting::factory()->create([
            'key' => 'original-key',
        ]);

        Livewire::test(EditSystemSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'key' => 'updated-key',
            ])
            ->call('save');

        $this->assertDatabaseHas('system_settings', [
            'id' => $setting->id,
            'key' => 'updated-key',
        ]);
    }

    public function test_can_delete_system_setting(): void
    {
        $setting = SystemSetting::factory()->create();

        Livewire::test(EditSystemSetting::class, ['record' => $setting->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('system_settings', [
            'id' => $setting->id,
        ]);
    }
}
