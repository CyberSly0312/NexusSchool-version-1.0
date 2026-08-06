<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableVersion>
 */
class TimetableVersionFactory extends Factory
{
    protected $model = TimetableVersion::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'version' => 1,
            'status' => 'draft',
            'snapshot' => [],
            'reason' => null,
            'created_by' => null,
            'created_at' => now(),
        ];
    }
}
