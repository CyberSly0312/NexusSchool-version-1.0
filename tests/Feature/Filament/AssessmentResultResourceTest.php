<?php

namespace Tests\Feature\Filament;

use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\School\Models\School;
use App\Models\AcademicClass;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssessmentResultResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected School $school;

    protected Assessment $assessment;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school',
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->school = School::find($schoolId);
        $year = AcademicYear::factory()->create(['school_id' => $this->school->id]);
        $term = AcademicTerm::factory()->create([
            'school_id' => $this->school->id,
            'academic_year_id' => $year->id,
        ]);
        $category = SubjectCategory::factory()->create(['school_id' => $this->school->id]);
        $subject = Subject::factory()->create([
            'school_id' => $this->school->id,
            'subject_category_id' => $category->id,
        ]);
        DB::table('programs')->insert([
            'uuid' => Str::ulid(), 'id' => 1, 'school_id' => $this->school->id, 'name' => 'Test Program', 'code' => 'P001',
            'educational_level' => 'primary', 'duration' => '1 year', 'order' => 1, 'status' => 'active',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('grade_levels')->insert([
            'uuid' => Str::ulid(), 'id' => 1, 'program_id' => 1, 'name' => 'Grade 1', 'code' => 'G1',
            'level_order' => 1, 'educational_cycle' => 'primary', 'status' => 'active',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('sections')->insert([
            'uuid' => Str::ulid(), 'id' => 1, 'grade_level_id' => 1, 'name' => 'Section A', 'code' => 'SA',
            'order' => 1, 'status' => 'active',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $class = AcademicClass::factory()->create([
            'academic_year_id' => $year->id,
            'program_id' => 1,
            'grade_level_id' => 1,
            'section_id' => 1,
        ]);
        $user = User::factory()->create();
        $this->assessment = Assessment::factory()->create([
            'school_id' => $this->school->id,
            'academic_year_id' => $year->id,
            'academic_term_id' => $term->id,
            'subject_id' => $subject->id,
            'academic_class_id' => $class->id,
            'user_id' => $user->id,
        ]);
        $this->student = Student::factory()->create(['school_id' => $this->school->id]);
        StudentEnrollment::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $year->id,
            'enrollment_status' => 'active',
        ]);
    }

    public function test_can_list_assessment_results(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->count(3)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->assessment->academic_year_id,
            'academic_term_id' => $this->assessment->academic_term_id,
        ]);

        $this->get('/admin/assessment-results')
            ->assertStatus(200);
    }

    public function test_can_view_create_page(): void
    {
        $this->get('/admin/assessment-results/create')
            ->assertStatus(200);
    }

    public function test_can_view_edit_page(): void
    {
        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->assessment->academic_year_id,
            'academic_term_id' => $this->assessment->academic_term_id,
        ]);

        $this->get("/admin/assessment-results/{$result->id}/edit")
            ->assertStatus(200);
    }
}
