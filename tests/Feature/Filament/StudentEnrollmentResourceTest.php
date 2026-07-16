<?php

namespace Tests\Feature\Filament;

use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentEnrollmentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $schoolId;

    protected Student $student;

    protected AcademicYear $academicYear;

    protected AcademicClass $academicClass;

    protected Program $program;

    protected GradeLevel $gradeLevel;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');

        $this->schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->student = Student::factory()
            ->forSchool($this->schoolId)
            ->create(['status' => 'active']);

        $this->academicYear = AcademicYear::factory()
            ->create(['school_id' => $this->schoolId]);

        $this->program = Program::factory()
            ->create(['school_id' => $this->schoolId]);

        $this->gradeLevel = GradeLevel::factory()
            ->create(['program_id' => $this->program->id]);

        $this->section = Section::factory()
            ->create(['grade_level_id' => $this->gradeLevel->id]);

        $this->academicClass = AcademicClass::factory()
            ->create([
                'academic_year_id' => $this->academicYear->id,
                'program_id' => $this->program->id,
                'grade_level_id' => $this->gradeLevel->id,
                'section_id' => $this->section->id,
            ]);
    }

    public function test_can_render_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/student-enrollments')
            ->assertSuccessful();
    }

    public function test_can_render_create_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/student-enrollments/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_page(): void
    {
        $enrollment = StudentEnrollment::factory()
            ->forSchool($this->schoolId)
            ->forStudent($this->student->id)
            ->forAcademicYear($this->academicYear->id)
            ->create([
                'academic_class_id' => $this->academicClass->id,
                'program_id' => $this->program->id,
                'grade_level_id' => $this->gradeLevel->id,
                'section_id' => $this->section->id,
            ]);

        $this->actingAs($this->admin)
            ->get('/admin/student-enrollments/'.$enrollment->id.'/edit')
            ->assertSuccessful();
    }
}
