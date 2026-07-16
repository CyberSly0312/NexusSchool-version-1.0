<?php

namespace Database\Seeders;

use App\Domains\Department\Enums\DepartmentStatus;
use App\Models\Department;
use App\Models\School;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'demo-school')->first();

        if (! $school) {
            return;
        }

        $departments = [
            ['name' => 'Science Department', 'code' => 'SCI', 'color' => '#22c55e', 'order' => 1],
            ['name' => 'Arts Department', 'code' => 'ART', 'color' => '#3b82f6', 'order' => 2],
            ['name' => 'Commerce Department', 'code' => 'COM', 'color' => '#f59e0b', 'order' => 3],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['school_id' => $school->id, 'name' => $dept['name']],
                [
                    'code' => $dept['code'],
                    'description' => "{$dept['name']} for the school.",
                    'color' => $dept['color'],
                    'order' => $dept['order'],
                    'status' => DepartmentStatus::Active->value,
                ],
            );
        }
    }
}
