<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Domains\Student\Enums\StudentStatus;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $students = [
            [
                'student_number' => 'STU-2024-001',
                'admission_number' => 'ADM-2024-001',
                'first_name' => 'Kofi',
                'last_name' => 'Asante',
                'gender' => 'male',
                'date_of_birth' => '2010-03-15',
                'nationality' => 'Ghanaian',
                'email' => 'kofi.asante@student.edu',
                'admission_date' => '2024-09-01',
            ],
            [
                'student_number' => 'STU-2024-002',
                'admission_number' => 'ADM-2024-002',
                'first_name' => 'Ama',
                'last_name' => 'Mensah',
                'gender' => 'female',
                'date_of_birth' => '2009-07-22',
                'nationality' => 'Ghanaian',
                'email' => 'ama.mensah@student.edu',
                'admission_date' => '2024-09-01',
            ],
            [
                'student_number' => 'STU-2024-003',
                'admission_number' => 'ADM-2024-003',
                'first_name' => 'Kwadwo',
                'last_name' => 'Ofori',
                'gender' => 'male',
                'date_of_birth' => '2011-01-10',
                'nationality' => 'Ghanaian',
                'email' => 'kwadwo.ofori@student.edu',
                'admission_date' => '2024-09-01',
            ],
            [
                'student_number' => 'STU-2024-004',
                'admission_number' => 'ADM-2024-004',
                'first_name' => 'Efua',
                'last_name' => 'Boateng',
                'gender' => 'female',
                'date_of_birth' => '2010-11-05',
                'nationality' => 'Ghanaian',
                'email' => 'efua.boateng@student.edu',
                'admission_date' => '2024-09-01',
            ],
            [
                'student_number' => 'STU-2024-005',
                'admission_number' => 'ADM-2024-005',
                'first_name' => 'Yaw',
                'last_name' => 'Amponsah',
                'gender' => 'male',
                'date_of_birth' => '2009-05-30',
                'nationality' => 'Ghanaian',
                'email' => 'yaw.amponsah@student.edu',
                'admission_date' => '2024-09-01',
            ],
        ];

        foreach ($students as $studentData) {
            Student::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'student_number' => $studentData['student_number'],
                ],
                array_merge($studentData, [
                    'school_id' => $school->id,
                    'status' => StudentStatus::Active->value,
                ]),
            );
        }
    }
}
