<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AcademicYearSeeder::class,
            AcademicTermSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            GradeLevelSeeder::class,
            SectionSeeder::class,
            AcademicClassSeeder::class,
            ClassroomSeeder::class,
            SubjectCategorySeeder::class,
            SubjectSeeder::class,
            SubjectCoefficientSeeder::class,
            GuardianSeeder::class,
            StudentSeeder::class,
            StudentEnrollmentSeeder::class,
            UserSeeder::class,
            AssessmentSeeder::class,
            AssessmentResultSeeder::class,
        ]);
    }
}
