<?php

namespace Database\Seeders;

use App\Domains\Program\Enums\EducationalLevel;
use App\Domains\Program\Enums\ProgramStatus;
use App\Models\Department;
use App\Models\Program;
use App\Models\School;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'demo-school')->first();

        if (! $school) {
            return;
        }

        $scienceDept = Department::where('school_id', $school->id)
            ->where('name', 'Science Department')
            ->first();

        $artsDept = Department::where('school_id', $school->id)
            ->where('name', 'Arts Department')
            ->first();

        $programs = [
            [
                'name' => 'Junior High School',
                'code' => 'JHS',
                'educational_level' => EducationalLevel::JuniorSecondary,
                'duration' => '3 years',
                'department_id' => null,
                'order' => 1,
            ],
            [
                'name' => 'Senior High School',
                'code' => 'SHS',
                'educational_level' => EducationalLevel::SeniorSecondary,
                'duration' => '3 years',
                'department_id' => null,
                'order' => 2,
            ],
            [
                'name' => 'Science Program',
                'code' => 'SCI',
                'educational_level' => EducationalLevel::SeniorSecondary,
                'duration' => '3 years',
                'department_id' => $scienceDept?->id,
                'order' => 3,
            ],
            [
                'name' => 'Arts Program',
                'code' => 'ART',
                'educational_level' => EducationalLevel::SeniorSecondary,
                'duration' => '3 years',
                'department_id' => $artsDept?->id,
                'order' => 4,
            ],
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(
                ['school_id' => $school->id, 'name' => $program['name']],
                [
                    'code' => $program['code'],
                    'description' => "{$program['name']} program.",
                    'educational_level' => $program['educational_level']->value,
                    'duration' => $program['duration'],
                    'department_id' => $program['department_id'],
                    'order' => $program['order'],
                    'status' => ProgramStatus::Active->value,
                ],
            );
        }
    }
}
