<?php

namespace Tests\Feature\Filament;

use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class GradeLevelResourceTest extends TestCase
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

    public function test_can_render_grade_level_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/grade-levels')
            ->assertSuccessful();
    }

    public function test_can_render_create_grade_level_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/grade-levels/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_grade_level_page(): void
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

        $program = Program::factory()
            ->forSchool($schoolId)
            ->create();

        $gradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->withOrder(1)
            ->create();

        $this->actingAs($this->admin)
            ->get('/school-admin/grade-levels/'.$gradeLevel->id.'/edit')
            ->assertSuccessful();
    }
}
