<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Domains\School\Models\School;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AssessmentResultSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first() ?? School::factory()->create();
        $assessments = Assessment::where('school_id', $school->id)->get();

        if ($assessments->isEmpty()) {
            $this->command?->warn('No assessments found. Run AssessmentSeeder first.');

            return;
        }

        foreach ($assessments as $assessment) {
            $students = Student::where('school_id', $school->id)
                ->inRandomOrder()
                ->take(fake()->numberBetween(3, 8))
                ->get();

            foreach ($students as $student) {
                AssessmentResult::factory()
                    ->forSchool($school)
                    ->create([
                        'assessment_id' => $assessment->id,
                        'student_id' => $student->id,
                        'subject_id' => $assessment->subject_id,
                        'academic_year_id' => $assessment->academic_year_id,
                        'academic_term_id' => $assessment->academic_term_id,
                    ]);
            }
        }
    }
}
