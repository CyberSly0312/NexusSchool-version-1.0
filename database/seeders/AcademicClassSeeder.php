<?php

namespace Database\Seeders;

use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Domains\School\Models\School;
use App\Models\Section;
use Illuminate\Database\Seeder;

class AcademicClassSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $academicYear = AcademicYear::where('school_id', $school->id)
            ->where('name', '2025-2026')
            ->first();

        if (! $academicYear) {
            return;
        }

        $programs = Program::where('school_id', $school->id)->get();

        foreach ($programs as $program) {
            $gradeLevels = GradeLevel::where('program_id', $program->id)->get();

            foreach ($gradeLevels as $gradeLevel) {
                $sections = Section::where('grade_level_id', $gradeLevel->id)->get();

                foreach ($sections as $index => $section) {
                    $classCode = strtoupper($gradeLevel->code.'-'.$section->code);

                    AcademicClass::firstOrCreate(
                        [
                            'academic_year_id' => $academicYear->id,
                            'name' => $gradeLevel->name.' '.$section->name,
                        ],
                        [
                            'program_id' => $program->id,
                            'grade_level_id' => $gradeLevel->id,
                            'section_id' => $section->id,
                            'code' => $classCode,
                            'description' => "Class {$gradeLevel->name} - {$section->name}",
                            'capacity' => 40,
                            'display_order' => $index + 1,
                            'status' => AcademicClassStatus::Active->value,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
