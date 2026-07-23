<?php

namespace Database\Seeders;

use App\Domains\Subject\Enums\SubjectStatus;
use App\Domains\School\Models\School;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $categories = [
            'LANG' => 'Langues',
            'SCI' => 'Sciences',
            'HUM' => 'Humanités',
            'TECH' => 'Technique',
            'BIZ' => 'Commerce',
            'ARTS' => 'Arts',
            'SPT' => 'Sports',
        ];

        $subjects = [
            ['name' => 'Mathématiques', 'code' => 'MATH', 'short_name' => 'Math', 'category' => 'SCI', 'description' => 'Mathématiques et algèbre', 'display_order' => 1, 'color' => '#3B82F6', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 5],
            ['name' => 'Physique', 'code' => 'PHY', 'short_name' => 'Phys', 'category' => 'SCI', 'description' => 'Sciences physiques', 'display_order' => 2, 'color' => '#10B981', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Chimie', 'code' => 'CHEM', 'short_name' => 'Chim', 'category' => 'SCI', 'description' => 'Sciences chimiques', 'display_order' => 3, 'color' => '#8B5CF6', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Biologie', 'code' => 'BIO', 'short_name' => 'Bio', 'category' => 'SCI', 'description' => 'Sciences du vivant', 'display_order' => 4, 'color' => '#06B6D4', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 3],
            ['name' => 'Français', 'code' => 'FRE', 'short_name' => 'Fr', 'category' => 'LANG', 'description' => 'Études de la langue française', 'display_order' => 5, 'color' => '#EC4899', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Anglais', 'code' => 'ENG', 'short_name' => 'En', 'category' => 'LANG', 'description' => 'Études de la langue anglaise', 'display_order' => 6, 'color' => '#F59E0B', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 4],
            ['name' => 'Histoire-Géographie', 'code' => 'HIST', 'short_name' => 'H/G', 'category' => 'HUM', 'description' => 'Études historiques et géographiques', 'display_order' => 7, 'color' => '#F97316', 'is_core_subject' => true, 'is_elective' => false, 'weekly_hours' => 3],
            ['name' => 'Informatique', 'code' => 'CS', 'short_name' => 'Info', 'category' => 'TECH', 'description' => 'Informatique et programmation', 'display_order' => 8, 'color' => '#14B8A6', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Économie', 'code' => 'ECON', 'short_name' => 'Éco', 'category' => 'BIZ', 'description' => 'Études économiques', 'display_order' => 9, 'color' => '#A855F7', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Comptabilité', 'code' => 'ACC', 'short_name' => 'Compta', 'category' => 'BIZ', 'description' => 'Principes de comptabilité', 'display_order' => 10, 'color' => '#EF4444', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 3],
            ['name' => 'Dessin Technique', 'code' => 'TD', 'short_name' => 'DT', 'category' => 'TECH', 'description' => 'Dessin et conception technique', 'display_order' => 11, 'color' => '#0EA5E9', 'is_core_subject' => false, 'is_elective' => true, 'weekly_hours' => 2],
        ];

        foreach ($subjects as $subjectData) {
            $categoryCode = $subjectData['category'];
            $category = SubjectCategory::where('school_id', $school->id)->where('code', $categoryCode)->first();

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
