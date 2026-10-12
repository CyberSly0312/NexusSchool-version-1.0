<?php

namespace Tests\Feature\Filament;

use App\Models\Classroom;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClassroomResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => (string) Str::ulid(),
        ]);

        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);
$this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    public function test_can_render_classroom_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/classrooms')
            ->assertSuccessful();
    }

    public function test_can_render_create_classroom_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/classrooms/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_classroom_page(): void
    {
        $classroom = Classroom::factory()->create();

        $this->actingAs($this->admin)
            ->get('/school-admin/classrooms/'.$classroom->id.'/edit')
            ->assertSuccessful();
    }
}
