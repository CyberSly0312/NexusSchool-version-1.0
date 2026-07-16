<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleResourceTest extends TestCase
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

    public function test_can_render_role_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/roles')
            ->assertSuccessful();
    }

    public function test_can_render_create_role_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/roles/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_role_page(): void
    {
        $role = Role::create(['name' => 'test_role', 'guard_name' => 'web']);

        $this->actingAs($this->admin)
            ->get('/admin/roles/'.$role->id.'/edit')
            ->assertSuccessful();
    }
}
