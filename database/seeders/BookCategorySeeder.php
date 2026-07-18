<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\BookCategory;
use Illuminate\Database\Seeder;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Novels, short stories, and fictional works across all genres.',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Science',
                'description' => 'Textbooks and reference materials for physics, chemistry, and biology.',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Mathematics',
                'description' => 'Mathematics textbooks, problem sets, and reference guides.',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'History',
                'description' => 'Historical texts covering African and world history.',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Literature',
                'description' => 'Poetry, drama, literary criticism, and English language studies.',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Technology',
                'description' => 'Computer science, ICT, and technology-related materials.',
                'parent_id' => null,
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            BookCategory::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $category['name'],
                ],
                $category
            );
        }
    }
}
