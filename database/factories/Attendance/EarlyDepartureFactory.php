<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\EarlyDeparture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EarlyDeparture>
 */
class EarlyDepartureFactory extends Factory
{
    protected $model = EarlyDeparture::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'attendance_record_id' => AttendanceRecord::factory(),
            'minutes' => fake()->numberBetween(1, 60),
            'departed_at' => now(),
            'recorded_by' => null,
        ];
    }
}
