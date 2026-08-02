<?php

declare(strict_types=1);

namespace Database\Seeders\Student;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\School\Models\School;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if ($school === null) {
            return;
        }

        $year = AcademicCalendarYear::query()
            ->forSchool($school->id)
            ->active()
            ->first();

        if ($year === null) {
            return;
        }

        $classes = SchoolClass::query()
            ->forSchool($school->id)
            ->limit(4)
            ->get();

        if ($classes->isEmpty()) {
            return;
        }

        $classes->each(function (SchoolClass $class, int $index) use ($school, $year): void {
            Student::factory()
                ->count(8)
                ->create([
                    'school_id' => $school->id,
                    'academic_calendar_year_id' => $year->id,
                    'school_class_id' => $class->id,
                    'admission_date' => $year->start_date?->toDateString() ?? now()->toDateString(),
                ]);
        });
    }
}
