<?php

namespace Tests\Feature\Filament;

use App\Models\Route;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_routes_list_page(): void
    {
        $this->get(route('filament.admin.resources.routes.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_route_page(): void
    {
        $this->get(route('filament.admin.resources.routes.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_route_page(): void
    {
        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $route = Route::factory()->create(['school_id' => $schoolId]);

        $this->get(route('filament.admin.resources.routes.edit', ['record' => $route]))
            ->assertSuccessful();
    }
}
