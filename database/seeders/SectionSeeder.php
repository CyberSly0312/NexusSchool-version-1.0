<?php

namespace Database\Seeders;

use App\Domains\Section\Enums\SectionStatus;
use App\Models\GradeLevel;
use App\Domains\School\Models\School;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $juniorHigh = GradeLevel::whereHas('program', fn ($q) => $q->where('school_id', $school->id))
            ->where('name', '6ème')
            ->first();

        if ($juniorHigh) {
            $sections = ['A', 'B', 'C', 'D'];

            foreach ($sections as $i => $name) {
                Section::firstOrCreate(
                    ['grade_level_id' => $juniorHigh->id, 'name' => $name],
                    [
                        'code' => $name,
                        'description' => "Section {$name}.",
                        'order' => $i + 1,
                        'status' => SectionStatus::Active->value,
                    ],
                );
            }
        }

        $seniorHigh = GradeLevel::whereHas('program', fn ($q) => $q->where('school_id', $school->id))
            ->where('name', 'Seconde')
            ->first();

        if ($seniorHigh) {
            $shsSections = ['Science', 'Arts', 'Commercial'];

            foreach ($shsSections as $i => $name) {
                Section::firstOrCreate(
                    ['grade_level_id' => $seniorHigh->id, 'name' => $name],
                    [
                        'code' => strtoupper(substr($name, 0, 3)),
                        'description' => "{$name} section.",
                        'order' => $i + 1,
                        'status' => SectionStatus::Active->value,
                    ],
                );
            }
        }
    }
}
