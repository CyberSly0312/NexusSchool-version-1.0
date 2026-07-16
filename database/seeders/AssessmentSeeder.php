<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Domains\School\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first() ?? School::factory()->create();
        $year = AcademicYear::where('school_id', $school->id)->first() ?? AcademicYear::factory()->forSchool($school)->create();
        $term = AcademicTerm::where('academic_year_id', $year->id)->first() ?? AcademicTerm::factory()->forSchool($school)->for('academicYear', fn() => $year)->create();
        $subjects = Subject::where('school_id', $school->id)->get();
        $classes = AcademicClass::where('school_id', $school->id)->get();
        $user = User::where('school_id', $school->id)->first() ?? User::factory()->forSchool($school)->create();

        if ($subjects->isEmpty()) {
            $this->command?->warn('No subjects found. Skipping AssessmentSeeder.');

            return;
        }

        foreach ($subjects->take(3) as $subject) {
            Assessment::factory()
                ->forSchool($school)
                ->create([
                    'academic_year_id' => $year->id,
                    'academic_term_id' => $term->id,
                    'subject_id' => $subject->id,
                    'academic_class_id' => $classes->isNotEmpty() ? $classes->random()->id : AcademicClass::factory()->forSchool($school)->create()->id,
                    'user_id' => $user->id,
                ]);
        }
    }
}
