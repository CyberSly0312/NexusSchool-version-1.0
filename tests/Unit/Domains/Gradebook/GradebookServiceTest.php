<?php

namespace Tests\Unit\Domains\Gradebook;

use App\Domains\Gradebook\DTOs\AssessmentResultData;
use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\Gradebook\Enums\LetterGrade;
use App\Domains\Gradebook\Services\GradebookService;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Domains\School\Models\School;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\AcademicClass;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\SubjectCategory;
use App\Models\SubjectCoefficient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GradebookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected AcademicYear $year;
    protected AcademicTerm $term;
    protected Subject $subject;
    protected Assessment $assessment;
    protected Student $student;
    protected GradebookService $service;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->year = AcademicYear::factory()->create(['school_id' => $this->school->id]);
        $this->term = AcademicTerm::factory()->create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->year->id,
        ]);
        $category = SubjectCategory::factory()->create(['school_id' => $this->school->id]);
        $this->subject = Subject::factory()->create([
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
            'academic_year_id' => $this->year->id,
            'program_id' => 1,
            'grade_level_id' => 1,
            'section_id' => 1,
        ]);
        $user = User::factory()->create();
        $this->assessment = Assessment::factory()->create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'subject_id' => $this->subject->id,
            'academic_class_id' => $class->id,
            'user_id' => $user->id,
            'maximum_score' => 100,
            'passing_score' => 50,
            'weight' => 2,
        ]);
        $this->student = Student::factory()->create(['school_id' => $this->school->id]);
        StudentEnrollment::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'enrollment_status' => 'active',
        ]);
        $this->service = app(GradebookService::class);

        SubjectCoefficient::factory()->create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'coefficient' => 2.5,
            'effective_from' => now()->subMonth(),
            'effective_until' => now()->addYear(),
            'is_active' => true,
        ]);
    }

    public function test_can_record_result(): void
    {
        $data = AssessmentResultData::fromArray([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'assessment_id' => $this->assessment->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 85,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft->value,
            'is_absent' => false,
        ]);

        $result = $this->service->record($data);

        $this->assertInstanceOf(AssessmentResult::class, $result);
        $this->assertEquals(85.0, $result->percentage);
        $this->assertEquals('B+', $result->letter_grade);
        $this->assertEquals(3.3, $result->grade_point);
        $this->assertEquals(2.5, $result->coefficient);
        $this->assertEquals(212.5, $result->weighted_score);
        $this->assertEquals(AssessmentResultStatus::Draft->value, $result->status->value);
    }

    public function test_records_absent_result(): void
    {
        $data = AssessmentResultData::fromArray([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'assessment_id' => $this->assessment->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 0,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft->value,
            'is_absent' => true,
        ]);

        $result = $this->service->record($data);

        $this->assertEquals(0.0, $result->percentage);
        $this->assertNull($result->letter_grade);
        $this->assertNull($result->grade_point);
        $this->assertNull($result->weighted_score);
    }

    public function test_throws_when_score_exceeds_maximum(): void
    {
        $this->expectException(ValidationException::class);

        $data = AssessmentResultData::fromArray([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'assessment_id' => $this->assessment->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 150,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft->value,
            'is_absent' => false,
        ]);

        $this->service->record($data);
    }

    public function test_throws_for_duplicate_result(): void
    {
        $this->expectException(ValidationException::class);

        $data = AssessmentResultData::fromArray([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'assessment_id' => $this->assessment->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 75,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft->value,
            'is_absent' => false,
        ]);

        $this->service->record($data);
        $this->service->record($data);
    }

    public function test_throws_for_unenrolled_student(): void
    {
        $this->expectException(ValidationException::class);

        $unenrolled = Student::factory()->forSchool($this->school)->create();

        $data = AssessmentResultData::fromArray([
            'school_id' => $this->school->id,
            'student_id' => $unenrolled->id,
            'assessment_id' => $this->assessment->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 65,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft->value,
            'is_absent' => false,
        ]);

        $this->service->record($data);
    }

    public function test_can_update_draft_result(): void
    {
        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 50,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Draft,
        ]);

        $updated = $this->service->update($result->id, ['raw_score' => 90]);

        $this->assertEquals(90.0, $updated->raw_score);
        $this->assertEquals(90.0, $updated->percentage);
        $this->assertEquals('A-', $updated->letter_grade);
    }

    public function test_throws_when_updating_published_result(): void
    {
        $this->expectException(ValidationException::class);

        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 80,
            'maximum_score' => 100,
            'status' => AssessmentResultStatus::Published,
        ]);

        $this->service->update($result->id, ['raw_score' => 95]);
    }

    public function test_can_delete_draft_result(): void
    {
        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Draft,
        ]);

        $deleted = $this->service->delete($result->id);

        $this->assertTrue($deleted);
    }

    public function test_throws_when_deleting_published_result(): void
    {
        $this->expectException(ValidationException::class);

        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Published,
        ]);

        $this->service->delete($result->id);
    }

    public function test_can_verify_result(): void
    {
        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Draft,
        ]);

        $verified = $this->service->verify($result->id);

        $this->assertEquals(AssessmentResultStatus::Verified->value, $verified->status->value);
    }

    public function test_throws_when_verifying_locked_result(): void
    {
        $this->expectException(ValidationException::class);

        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Locked,
        ]);

        $this->service->verify($result->id);
    }

    public function test_can_publish_verified_results(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->count(3)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Verified,
        ]);

        $count = $this->service->publish($this->assessment->id);

        $this->assertEquals(3, $count);
    }

    public function test_throws_when_no_verified_results_to_publish(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->publish($this->assessment->id);
    }

    public function test_can_lock_published_result(): void
    {
        $result = AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'status' => AssessmentResultStatus::Published,
        ]);

        $locked = $this->service->lock($result->id);

        $this->assertEquals(AssessmentResultStatus::Locked->value, $locked->status->value);
        $this->assertTrue($locked->is_locked);
    }

    public function test_letter_grade_mapping(): void
    {
        $this->assertEquals('A+', LetterGrade::fromPercentage(98)->value);
        $this->assertEquals('A', LetterGrade::fromPercentage(95)->value);
        $this->assertEquals('A-', LetterGrade::fromPercentage(91)->value);
        $this->assertEquals('B+', LetterGrade::fromPercentage(88)->value);
        $this->assertEquals('B', LetterGrade::fromPercentage(84)->value);
        $this->assertEquals('B-', LetterGrade::fromPercentage(80)->value);
        $this->assertEquals('C+', LetterGrade::fromPercentage(77)->value);
        $this->assertEquals('C', LetterGrade::fromPercentage(73)->value);
        $this->assertEquals('C-', LetterGrade::fromPercentage(70)->value);
        $this->assertEquals('D+', LetterGrade::fromPercentage(67)->value);
        $this->assertEquals('D', LetterGrade::fromPercentage(63)->value);
        $this->assertEquals('F', LetterGrade::fromPercentage(55)->value);
    }

    public function test_grade_point_mapping(): void
    {
        $this->assertEquals(4.0, LetterGrade::gradePointFromPercentage(98));
        $this->assertEquals(3.7, LetterGrade::gradePointFromPercentage(95));
        $this->assertEquals(3.3, LetterGrade::gradePointFromPercentage(88));
        $this->assertEquals(2.0, LetterGrade::gradePointFromPercentage(73));
        $this->assertEquals(0.0, LetterGrade::gradePointFromPercentage(55));
    }

    public function test_calculates_term_average(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 80,
            'maximum_score' => 100,
            'coefficient' => 2.5,
            'weighted_score' => 200,
            'percentage' => 80,
            'status' => AssessmentResultStatus::Verified,
        ]);

        $result = $this->service->calculateGradebook($this->student->id, $this->term->id, $this->year->id);

        $this->assertEquals(1, $result['result_count']);
        $this->assertEquals(80.0, $result['term_average']);
    }

    public function test_calculates_weighted_average(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 90,
            'maximum_score' => 100,
            'coefficient' => 2.5,
            'weighted_score' => 225,
            'percentage' => 90,
            'status' => AssessmentResultStatus::Verified,
        ]);

        $result = $this->service->calculateGradebook($this->student->id, $this->term->id, $this->year->id);

        $this->assertEquals(1, $result['result_count']);
        $this->assertEquals(2.0, $result['total_weight']);
        $this->assertEquals(90.0, $result['weighted_average']);
    }

    public function test_gradebook_returns_null_when_no_results(): void
    {
        $result = $this->service->calculateGradebook($this->student->id, $this->term->id, $this->year->id);

        $this->assertNull($result['term_average']);
        $this->assertEquals(0, $result['result_count']);
    }

    public function test_calculates_subject_average(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'raw_score' => 70,
            'maximum_score' => 100,
            'percentage' => 70,
            'status' => AssessmentResultStatus::Verified,
        ]);

        $avg = $this->service->getSubjectAverage($this->student->id, $this->subject->id, $this->term->id, $this->year->id);

        $this->assertEquals(70.0, $avg);
    }

    public function test_subject_average_returns_null_when_no_results(): void
    {
        $avg = $this->service->getSubjectAverage($this->student->id, $this->subject->id, $this->term->id, $this->year->id);

        $this->assertNull($avg);
    }

    public function test_calculates_rankings(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'percentage' => 95,
            'status' => AssessmentResultStatus::Entered,
        ]);

        AssessmentResult::factory()->forSchool($this->school)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => Student::factory()->forSchool($this->school)->create()->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
            'percentage' => 80,
            'status' => AssessmentResultStatus::Entered,
        ]);

        $rankings = $this->service->calculateRankings($this->assessment->id);

        $this->assertCount(2, $rankings);
    }

    public function test_rankings_returns_empty_when_no_results(): void
    {
        $rankings = $this->service->calculateRankings($this->assessment->id);

        $this->assertCount(0, $rankings);
    }

    public function test_can_bulk_record_results(): void
    {
        $student2 = Student::factory()->forSchool($this->school)->create();
        StudentEnrollment::factory()->forSchool($this->school)->create([
            'student_id' => $student2->id,
            'academic_year_id' => $this->year->id,
            'enrollment_status' => 'active',
        ]);

        $resultsData = [
            [
                'school_id' => $this->school->id,
                'student_id' => $this->student->id,
                'assessment_id' => $this->assessment->id,
                'subject_id' => $this->subject->id,
                'academic_year_id' => $this->year->id,
                'academic_term_id' => $this->term->id,
                'raw_score' => 75,
                'maximum_score' => 100,
                'status' => AssessmentResultStatus::Draft->value,
                'is_absent' => false,
            ],
            [
                'school_id' => $this->school->id,
                'student_id' => $student2->id,
                'assessment_id' => $this->assessment->id,
                'subject_id' => $this->subject->id,
                'academic_year_id' => $this->year->id,
                'academic_term_id' => $this->term->id,
                'raw_score' => 88,
                'maximum_score' => 100,
                'status' => AssessmentResultStatus::Draft->value,
                'is_absent' => false,
            ],
        ];

        $results = $this->service->bulkRecord($resultsData);

        $this->assertCount(2, $results);
    }

    public function test_status_transitions(): void
    {
        $draft = AssessmentResultStatus::Draft;
        $entered = AssessmentResultStatus::Entered;
        $verified = AssessmentResultStatus::Verified;
        $published = AssessmentResultStatus::Published;
        $locked = AssessmentResultStatus::Locked;

        $this->assertTrue($draft->canTransitionTo($entered));
        $this->assertFalse($draft->canTransitionTo($verified));
        $this->assertTrue($entered->canTransitionTo($verified));
        $this->assertTrue($entered->canTransitionTo($draft));
        $this->assertTrue($verified->canTransitionTo($published));
        $this->assertTrue($verified->canTransitionTo($entered));
        $this->assertTrue($published->canTransitionTo($locked));
        $this->assertTrue($published->canTransitionTo($verified));
        $this->assertFalse($locked->canTransitionTo($draft));
        $this->assertTrue($locked->isReadOnly());
        $this->assertTrue($published->isReadOnly());
        $this->assertTrue($draft->isDeletable());
        $this->assertTrue($entered->isDeletable());
        $this->assertFalse($verified->isDeletable());
    }

    public function test_paginates_results(): void
    {
        AssessmentResult::factory()->forSchool($this->school)->count(5)->create([
            'assessment_id' => $this->assessment->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->year->id,
            'academic_term_id' => $this->term->id,
        ]);

        $paginator = $this->service->paginate(['school_id' => $this->school->id], 3);

        $this->assertCount(3, $paginator);
        $this->assertEquals(5, $paginator->total());
    }
}
