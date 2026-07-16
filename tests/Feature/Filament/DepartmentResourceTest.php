<?php

namespace Tests\Feature\Filament;

use App\Models\Department;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DepartmentResourceTest extends TestCase
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

    public function test_can_render_department_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/departments')
            ->assertSuccessful();
    }

    public function test_can_render_create_department_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/departments/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_department_page(): void
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

        $department = Department::factory()
            ->forSchool($schoolId)
            ->create();

        $this->actingAs($this->admin)
            ->get('/admin/departments/'.$department->id.'/edit')
            ->assertSuccessful();
    }
}
