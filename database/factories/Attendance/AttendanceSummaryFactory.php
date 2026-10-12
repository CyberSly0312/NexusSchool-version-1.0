<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSummary>
 */
class AttendanceSummaryFactory extends Factory
{
    protected $model = AttendanceSummary::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'enrollment_id' => Enrollment::factory(),
            'period_type' => AttendancePeriod::TYPE_MONTHLY,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'sessions_count' => 20,
            'attended_count' => 18,
            'present_count' => 16,
            'late_count' => 2,
            'excused_count' => 1,
            'unexcused_count' => 1,
            'absent_count' => 1,
            'attendance_percentage' => 90.0,
            'late_percentage' => 10.0,
            'consecutive_absences' => 0,
            'consecutive_lates' => 0,
            'risk_score' => 10.0,
            'trend' => 0.0,
            'breakdown' => [],
            'calculated_at' => now(),
            'calculated_by' => null,
        ];
    }
}
