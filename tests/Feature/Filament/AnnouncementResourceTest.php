<?php

namespace Tests\Feature\Filament;

use App\Models\Announcement;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnnouncementResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active', 'school_id' => makeTenant()]);
        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);
$this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    public function test_can_render_announcements_list_page(): void
    {
        $this->get(route('filament.school-admin.resources.announcements.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_announcement_page(): void
    {
        $this->get(route('filament.school-admin.resources.announcements.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_announcement_page(): void
    {
        $announcement = Announcement::factory()->create([
            'school_id' => $this->admin->school_id,
            'author_id' => $this->admin->id,
        ]);

        $this->get(route('filament.school-admin.resources.announcements.edit', ['record' => $announcement]))
            ->assertSuccessful();
    }
}
