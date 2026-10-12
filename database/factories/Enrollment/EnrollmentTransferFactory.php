<?php

declare(strict_types=1);

namespace Database\Factories\Enrollment;

use App\Domains\Enrollment\Domain\Enums\TransferType;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnrollmentTransfer>
 */
class EnrollmentTransferFactory extends Factory
{
    protected $model = EnrollmentTransfer::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'enrollment_id' => \App\Domains\Enrollment\Infrastructure\Models\Enrollment::factory(),
            'student_id' => \App\Domains\Student\Infrastructure\Models\Student::factory(),
            'transfer_type' => TransferType::Internal,
            'from_class_id' => null,
            'to_class_id' => null,
            'from_section_id' => null,
            'to_section_id' => null,
            'from_school_id' => null,
            'to_school_id' => null,
            'reason' => fake()->optional()->sentence(),
            'transfer_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
