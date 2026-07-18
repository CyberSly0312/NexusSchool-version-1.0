<?php

namespace Tests\Feature\Filament;

use App\Models\Dashboard;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardResourceTest extends TestCase
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

    public function test_can_render_dashboard_list_page(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboards')->assertSuccessful();
    }

    public function test_can_render_create_dashboard_page(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboards/create')->assertSuccessful();
    }

    public function test_can_render_edit_dashboard_page(): void
    {
        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $dashboard = Dashboard::factory()->forSchool($schoolId)->create();

        $this->actingAs($this->admin)
            ->get('/admin/dashboards/'.$dashboard->id.'/edit')
            ->assertSuccessful();
    }

    public function test_can_render_view_dashboard_page(): void
    {
        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $dashboard = Dashboard::factory()->forSchool($schoolId)->create();

        $this->actingAs($this->admin)
            ->get('/admin/dashboards/'.$dashboard->id)
            ->assertSuccessful();
    }
}
