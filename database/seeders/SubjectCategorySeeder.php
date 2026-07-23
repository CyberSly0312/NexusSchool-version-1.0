<?php

namespace Database\Seeders;

use App\Domains\SubjectCategory\Enums\SubjectCategoryStatus;
use App\Domains\School\Models\School;
use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;

class SubjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $categories = [
            ['name' => 'Languages', 'code' => 'LANG', 'description' => 'Language studies including English, French, and local languages', 'display_order' => 1, 'color' => '#3B82F6', 'icon' => 'book-open'],
            ['name' => 'Sciences', 'code' => 'SCI', 'description' => 'Natural and physical sciences', 'display_order' => 2, 'color' => '#10B981', 'icon' => 'beaker'],
            ['name' => 'Humanities', 'code' => 'HUM', 'description' => 'History, geography, and social studies', 'display_order' => 3, 'color' => '#F59E0B', 'icon' => 'globe-alt'],
            ['name' => 'Technical', 'code' => 'TECH', 'description' => 'Technical and vocational subjects', 'display_order' => 4, 'color' => '#8B5CF6', 'icon' => 'calculator'],
            ['name' => 'Business', 'code' => 'BIZ', 'description' => 'Business and commerce studies', 'display_order' => 5, 'color' => '#EC4899', 'icon' => 'calculator'],
            ['name' => 'Arts', 'code' => 'ARTS', 'description' => 'Visual and performing arts', 'display_order' => 6, 'color' => '#F97316', 'icon' => 'musical-note'],
            ['name' => 'Sports', 'code' => 'SPT', 'description' => 'Physical education and sports', 'display_order' => 7, 'color' => '#06B6D4', 'icon' => 'academic-cap'],
            ['name' => 'Religious Education', 'code' => 'RE', 'description' => 'Moral and religious instruction', 'display_order' => 8, 'color' => '#64748B', 'icon' => 'academic-cap'],
        ];

        foreach ($categories as $category) {
            SubjectCategory::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'code' => $category['code'],
                ],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'display_order' => $category['display_order'],
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'status' => SubjectCategoryStatus::Active->value,
                ],
            );
        }
    }
}
