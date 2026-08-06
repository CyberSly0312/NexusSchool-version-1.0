<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Domain\Enums\AttendanceAlertStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceAlert>
 */
class AttendanceAlertFactory extends Factory
{
    protected $model = AttendanceAlert::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'enrollment_id' => Enrollment::factory(),
            'alert_type' => 'low_attendance',
            'severity' => 'high',
            'threshold' => 75.0,
            'observed_value' => 60.0,
            'payload' => [],
            'detected_at' => now(),
            'status' => AttendanceAlertStatus::Open,
        ];
    }
}
