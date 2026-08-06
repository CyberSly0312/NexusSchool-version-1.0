<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\LateArrival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LateArrival>
 */
class LateArrivalFactory extends Factory
{
    protected $model = LateArrival::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'attendance_record_id' => AttendanceRecord::factory(),
            'minutes' => fake()->numberBetween(1, 60),
            'arrived_at' => now(),
            'recorded_by' => null,
        ];
    }
}
