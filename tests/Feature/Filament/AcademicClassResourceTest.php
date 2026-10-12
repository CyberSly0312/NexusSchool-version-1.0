<?php

namespace Tests\Feature\Filament;

use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicClassResourceTest extends TestCase
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

    public function test_can_render_academic_class_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-classes')
            ->assertSuccessful();
    }

    public function test_can_render_create_academic_class_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-classes/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_academic_class_page(): void
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

        $academicYear = AcademicYear::factory()
            ->create(['school_id' => $schoolId]);

        $program = Program::factory()
            ->forSchool($schoolId)
            ->create();

        $gradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->withOrder(1)
            ->create();

        $section = Section::factory()
            ->forGradeLevel($gradeLevel->id)
            ->create();

        $academicClass = AcademicClass::factory()
            ->forAcademicYear($academicYear->id)
            ->forProgram($program->id)
            ->forGradeLevel($gradeLevel->id)
            ->forSection($section->id)
            ->create();

        $this->actingAs($this->admin)
            ->get('/school-admin/academic-classes/'.$academicClass->id.'/edit')
            ->assertSuccessful();
    }
}
