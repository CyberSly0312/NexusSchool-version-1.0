<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use App\Models\Route;
use App\Models\Student;
use App\Models\StudentTransportAssignment;
use Illuminate\Database\Seeder;

class StudentTransportAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $students = Student::where('school_id', $school->id)->take(3)->get();
        $routes = Route::where('school_id', $school->id)->where('status', 'active')->get();
        $academicYear = AcademicYear::where('school_id', $school->id)->first();

        if ($students->isEmpty() || $routes->isEmpty() || ! $academicYear) {
            return;
        }

        $stopNames = ['Morning Pickup', 'Afternoon Drop-off'];

        foreach ($students as $index => $student) {
            $route = $routes[$index % $routes->count()];

            StudentTransportAssignment::firstOrCreate(
                ['student_id' => $student->id, 'route_id' => $route->id],
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'stop_name' => $stopNames[$index % count($stopNames)],
                    'fare' => $route->fare,
                    'status' => 'active',
                ]
            );
        }
    }
}
