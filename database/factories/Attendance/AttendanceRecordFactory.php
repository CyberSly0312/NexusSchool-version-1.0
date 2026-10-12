<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'attendance_session_id' => AttendanceSession::factory(),
            'enrollment_id' => Enrollment::factory(),
            'status' => AttendanceStatus::Present,
            'recorded_at' => now(),
            'recorded_by' => null,
        ];
    }

    public function withStatus(AttendanceStatus|string $status): static
    {
        return $this->state(fn (): array => [
            'status' => $status instanceof AttendanceStatus ? $status : AttendanceStatus::from($status),
        ]);
    }

    public function present(): static
    {
        return $this->state(fn (): array => ['status' => AttendanceStatus::Present]);
    }

    public function late(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceStatus::Late,
            'late_minutes' => 15,
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn (): array => ['status' => AttendanceStatus::Absent]);
    }
}
