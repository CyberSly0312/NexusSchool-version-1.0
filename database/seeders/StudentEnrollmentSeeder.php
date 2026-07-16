<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Seeder;

class StudentEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $academicYear = AcademicYear::where('school_id', $school->id)->first();
        $academicClass = AcademicClass::where('school_id', $school->id)->first();
        $program = Program::where('school_id', $school->id)->first();
        $gradeLevel = GradeLevel::where('school_id', $school->id)->first();
        $section = Section::where('school_id', $school->id)->first();

        if (! $academicYear || ! $academicClass || ! $program || ! $gradeLevel || ! $section) {
            return;
        }

        $students = Student::where('school_id', $school->id)->get();

        foreach ($students as $i => $student) {
            StudentEnrollment::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'enrollment_number' => 'ENR-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                ],
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'academic_class_id' => $academicClass->id,
                    'program_id' => $program->id,
                    'grade_level_id' => $gradeLevel->id,
                    'section_id' => $section->id,
                    'enrollment_date' => now()->subDays(30)->format('Y-m-d'),
                    'effective_date' => now()->subDays(30)->format('Y-m-d'),
                    'enrollment_type' => EnrollmentType::NewAdmission->value,
                    'status' => EnrollmentStatus::Active->value,
                ],
            );
        }
    }
}
