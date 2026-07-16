<?php

namespace Database\Seeders;

use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicTermSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::where('name', '2025-2026')->first();

        if (! $year) {
            return;
        }

        AcademicTerm::firstOrCreate(
            ['academic_year_id' => $year->id, 'name' => 'Semester 1'],
            [
                'school_id' => $year->school_id,
                'code' => 'S1',
                'start_date' => '2025-10-01',
                'end_date' => '2026-01-31',
                'order' => 1,
                'status' => AcademicTermStatus::Active->value,
            ],
        );

        AcademicTerm::firstOrCreate(
            ['academic_year_id' => $year->id, 'name' => 'Semester 2'],
            [
                'school_id' => $year->school_id,
                'code' => 'S2',
                'start_date' => '2026-02-01',
                'end_date' => '2026-07-31',
                'order' => 2,
                'status' => AcademicTermStatus::Draft->value,
            ],
        );
    }
}
