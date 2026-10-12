<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\CorrectionReason;
use App\Domains\Attendance\Infrastructure\Models\AttendanceCorrection;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceCorrection>
 */
class AttendanceCorrectionFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'attendance_record_id' => AttendanceRecord::factory(),
            'previous_status' => AttendanceStatus::Absent,
            'new_status' => AttendanceStatus::Present,
            'reason_type' => CorrectionReason::TYPE_ERROR,
            'reason_text' => fake()->sentence(),
            'is_authorized' => true,
            'requested_by' => null,
            'authorized_by' => null,
            'authorized_at' => now(),
            'corrected_at' => now(),
            'notes' => null,
        ];
    }
}
