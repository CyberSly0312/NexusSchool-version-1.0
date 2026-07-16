<?php

namespace Database\Seeders;

use App\Domains\Subject\Enums\SubjectStatus;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'demo-school')->first();

        if (! $school) {
            return;
        }

        $categories = [
            'LANG' => 'Languages',
            'SCI' => 'Sciences',
            'HUM' => 'Humanities',
            'TECH' => 'Technical',
            'BIZ' => 'Business',
            'ARTS' => 'Arts',
            'SPT' => 'Sports',
        ];

        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'short_name' => 'Math', 'category' => 'SCI', 'description' => 'Mathematics and algebra', 'display_order' => 1, 'color' => '#3B82F6', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 5],
            ['name' => 'Physics', 'code' => 'PHY', 'short_name' => 'Phys', 'category' => 'SCI', 'description' => 'Physical sciences', 'display_order' => 2, 'color' => '#10B981', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'short_name' => 'Chem', 'category' => 'SCI', 'description' => 'Chemical sciences', 'display_order' => 3, 'color' => '#8B5CF6', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Biology', 'code' => 'BIO', 'short_name' => 'Bio', 'category' => 'SCI', 'description' => 'Life sciences', 'display_order' => 4, 'color' => '#06B6D4', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 3],
            ['name' => 'French', 'code' => 'FRE', 'short_name' => 'Fr', 'category' => 'LANG', 'description' => 'French language studies', 'display_order' => 5, 'color' => '#EC4899', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'English', 'code' => 'ENG', 'short_name' => 'En', 'category' => 'LANG', 'description' => 'English language studies', 'display_order' => 6, 'color' => '#F59E0B', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'History', 'code' => 'HIST', 'short_name' => 'Hist', 'category' => 'HUM', 'description' => 'Historical studies', 'display_order' => 7, 'color' => '#F97316', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 3],
            ['name' => 'Geography', 'code' => 'GEO', 'short_name' => 'Geo', 'category' => 'HUM', 'description' => 'Geographical studies', 'display_order' => 8, 'color' => '#64748B', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 3],
            ['name' => 'Computer Science', 'code' => 'CS', 'short_name' => 'CS', 'category' => 'TECH', 'description' => 'Computer science and programming', 'display_order' => 9, 'color' => '#14B8A6', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Economics', 'code' => 'ECON', 'short_name' => 'Eco', 'category' => 'BIZ', 'description' => 'Economic studies', 'display_order' => 10, 'color' => '#A855F7', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Accounting', 'code' => 'ACC', 'short_name' => 'Acc', 'category' => 'BIZ', 'description' => 'Accounting principles', 'display_order' => 11, 'color' => '#EF4444', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Technical Drawing', 'code' => 'TD', 'short_name' => 'TD', 'category' => 'TECH', 'description' => 'Technical drawing and design', 'display_order' => 12, 'color' => '#0EA5E9', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 2],
        ];

        foreach ($subjects as $subjectData) {
            $categoryCode = $subjectData['category'];
            $category = $school->subjectCategories()->where('code', $categoryCode)->first();

            if (! $category) {
                continue;
            }

            Subject::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'code' => $subjectData['code'],
                ],
                [
                    'subject_category_id' => $category->id,
                    'name' => $subjectData['name'],
                    'short_name' => $subjectData['short_name'],
                    'description' => $subjectData['description'],
                    'display_order' => $subjectData['display_order'],
                    'color' => $subjectData['color'],
                    'is_core_subject' => $subjectData['is_core_subject'],
                    'is_elective' => $subjectData['is_elective'],
                    'weekly_hours' => $subjectData['weekly_hours'],
                    'status' => SubjectStatus::Active->value,
                ],
            );
        }
    }
}
