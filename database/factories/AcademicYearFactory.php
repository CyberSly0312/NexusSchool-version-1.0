<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        return [
            'school_id' => 1,
            'name' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-07-31',
            'status' => 'active',
        ];
    }
}