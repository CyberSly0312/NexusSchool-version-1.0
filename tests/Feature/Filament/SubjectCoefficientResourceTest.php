<?php

namespace Tests\Feature\Filament;

use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\SubjectCoefficient;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubjectCoefficientResourceTest extends TestCase
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

    public function test_can_render_subject_coefficient_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/subject-coefficients')
            ->assertSuccessful();
    }

    public function test_can_render_create_subject_coefficient_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/subject-coefficients/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_subject_coefficient_page(): void
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

        $subject = Subject::factory()
            ->forSchool($schoolId)
            ->forCategory(SubjectCategory::factory()->forSchool($schoolId)->create()->id)
            ->create();
        $program = Program::factory()->forSchool($schoolId)->create();
        $gradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->create();

        $subjectCoefficient = SubjectCoefficient::factory()
            ->forSchool($schoolId)
            ->forSubject($subject->id)
            ->forProgram($program->id)
            ->forGradeLevel($gradeLevel->id)
            ->create();

        $this->actingAs($this->admin)
            ->get('/school-admin/subject-coefficients/'.$subjectCoefficient->id.'/edit')
            ->assertSuccessful();
    }
}
