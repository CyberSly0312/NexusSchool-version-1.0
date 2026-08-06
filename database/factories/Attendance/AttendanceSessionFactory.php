<?php

declare(strict_types=1);

namespace Database\Factories\Attendance;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use Database\Factories\Academic\AcademicProgramFactory;
use Database\Factories\Academic\SchoolClassFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', 'today');

        // Academic rows are school-scoped with unique indexes, so each nested
        // academic context gets its own fictional school id. Foreign keys are
        // not enforced in tests (DB_FOREIGN_KEYS=0) and callers always pass the
        // real session school_id explicitly.
        $academicSchoolId = 'acad-'.(string) \Illuminate\Support\Str::ulid();

        return [
            'school_id' => null,
            'school_class_id' => SchoolClassFactory::new()
                ->state([
                    'school_id' => $academicSchoolId,
                    'academic_program_id' => AcademicProgramFactory::new()
                        ->state([
                            'school_id' => $academicSchoolId,
                            'name' => 'Programme '.\Illuminate\Support\Str::random(6),
                        ]),
                ]),
            'date' => $date->format('Y-m-d'),
            'day_of_week' => ((int) $date->format('w') + 6) % 7,
            'period_number' => fake()->numberBetween(1, 8),
            'starts_at' => fake()->time(),
            'ends_at' => fake()->time(),
            'status' => AttendanceSessionStatus::Draft,
            'expected_students_count' => 0,
            'recorded_students_count' => 0,
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => AttendanceSessionStatus::Draft]);
    }

    public function open(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceSessionStatus::Open,
            'opened_at' => now(),
        ]);
    }

    public function takingAttendance(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceSessionStatus::TakingAttendance,
            'opened_at' => now(),
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceSessionStatus::Submitted,
            'opened_at' => now()->subHour(),
            'submitted_at' => now(),
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceSessionStatus::Locked,
            'opened_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'locked_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => AttendanceSessionStatus::Archived,
            'opened_at' => now()->subDay(),
            'submitted_at' => now()->subDay()->addMinutes(30),
            'locked_at' => now()->subDay()->addHour(),
            'archived_at' => now(),
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forClass(int|string $classId): static
    {
        return $this->state(fn (): array => ['school_class_id' => $classId]);
    }

    public function onDate(string $date, int $periodNumber): static
    {
        return $this->state(fn (): array => [
            'date' => $date,
            'day_of_week' => ((int) date('w', strtotime($date)) + 6) % 7,
            'period_number' => $periodNumber,
        ]);
    }
}
