<?php

namespace Database\Seeders;

use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Domains\School\Models\School;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectCoefficient;
use Illuminate\Database\Seeder;

class SubjectCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $subjects = Subject::where('school_id', $school->id)->take(5)->get();
        $programs = Program::where('school_id', $school->id)->take(2)->get();
        $gradeLevels = GradeLevel::whereIn('program_id', $programs->pluck('id'))->take(3)->get();

        foreach ($subjects as $subject) {
            foreach ($programs as $program) {
                foreach ($gradeLevels as $gradeLevel) {
                    SubjectCoefficient::firstOrCreate(
                        [
                            'school_id' => $school->id,
                            'subject_id' => $subject->id,
                            'program_id' => $program->id,
                            'grade_level_id' => $gradeLevel->id,
                            'academic_year_id' => null,
                        ],
                        [
                            'coefficient' => fake()->randomFloat(2, 0.5, 3),
                            'effective_from' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                            'effective_to' => fake()->optional(0.5)->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
                            'notes' => fake()->optional()->sentence(),
                            'status' => SubjectCoefficientStatus::Active->value,
                        ]
                    );
                }
            }
        }
    }
}
