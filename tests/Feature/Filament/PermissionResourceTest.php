<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_permission_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/permissions')
            ->assertSuccessful();
    }

    public function test_can_render_create_permission_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/permissions/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_permission_page(): void
    {
        $permission = Permission::create(['name' => 'school.view', 'guard_name' => 'web']);

        $this->actingAs($this->admin)
            ->get('/admin/permissions/'.$permission->id.'/edit')
            ->assertSuccessful();
    }
}
