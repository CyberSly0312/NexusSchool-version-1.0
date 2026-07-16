<?php

namespace Database\Seeders;

use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Models\School;
use App\Models\SubjectCoefficient;
use Illuminate\Database\Seeder;

class SubjectCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'demo-school')->first();

        if (! $school) {
            return;
        }

        $subjects = $school->subjects()->take(5)->get();
        $programs = $school->programs()->take(2)->get();
        $gradeLevels = $school->gradeLevels()->take(3)->get();

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
                            'effective_to' => fake()->optional(0.5)->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                            'notes' => fake()->optional()->sentence(),
                            'status' => SubjectCoefficientStatus::Active->value,
                        ]
                    );
                }
            }
        }
    }
}
