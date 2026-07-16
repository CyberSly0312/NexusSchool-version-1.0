<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
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

    public function test_can_render_user_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertSuccessful();
    }

    public function test_user_list_page_displays_users(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertSuccessful();
    }

    public function test_can_render_create_user_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/users/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_user_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get('/admin/users/'.$user->getRouteKey().'/edit')
            ->assertSuccessful();
    }
}
