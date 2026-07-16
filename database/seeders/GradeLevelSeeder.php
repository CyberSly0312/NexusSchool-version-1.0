<?php

namespace Database\Seeders;

use App\Domains\GradeLevel\Enums\EducationalCycle;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\School;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'demo-school')->first();

        if (! $school) {
            return;
        }

        $juniorHigh = Program::where('school_id', $school->id)
            ->where('name', 'Junior High School')
            ->first();

        $seniorHigh = Program::where('school_id', $school->id)
            ->where('name', 'Senior High School')
            ->first();

        if ($juniorHigh) {
            $jhsLevels = [
                ['name' => '6ème', 'code' => '6E', 'short_name' => '6ème', 'order' => 1],
                ['name' => '5ème', 'code' => '5E', 'short_name' => '5ème', 'order' => 2],
                ['name' => '4ème', 'code' => '4E', 'short_name' => '4ème', 'order' => 3],
                ['name' => '3ème', 'code' => '3E', 'short_name' => '3ème', 'order' => 4],
            ];

            foreach ($jhsLevels as $level) {
                GradeLevel::firstOrCreate(
                    ['program_id' => $juniorHigh->id, 'name' => $level['name']],
                    [
                        'code' => $level['code'],
                        'short_name' => $level['short_name'],
                        'level_order' => $level['order'],
                        'educational_cycle' => EducationalCycle::JuniorSecondary->value,
                        'description' => "{$level['name']} level.",
                        'status' => GradeLevelStatus::Active->value,
                    ],
                );
            }
        }

        if ($seniorHigh) {
            $shsLevels = [
                ['name' => 'Seconde', 'code' => 'SEC', 'short_name' => 'Sec', 'order' => 1],
                ['name' => 'Première', 'code' => 'PRE', 'short_name' => 'Pre', 'order' => 2],
                ['name' => 'Terminale', 'code' => 'TER', 'short_name' => 'Ter', 'order' => 3],
            ];

            foreach ($shsLevels as $level) {
                GradeLevel::firstOrCreate(
                    ['program_id' => $seniorHigh->id, 'name' => $level['name']],
                    [
                        'code' => $level['code'],
                        'short_name' => $level['short_name'],
                        'level_order' => $level['order'],
                        'educational_cycle' => EducationalCycle::SeniorSecondary->value,
                        'description' => "{$level['name']} level.",
                        'status' => GradeLevelStatus::Active->value,
                    ],
                );
            }
        }
    }
}
