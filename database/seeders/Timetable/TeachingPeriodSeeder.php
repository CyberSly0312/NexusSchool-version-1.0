<?php

declare(strict_types=1);

namespace Database\Seeders\Timetable;

use App\Domains\School\Models\School;
use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use Illuminate\Database\Seeder;

/**
 * Materializes the config-driven teaching grid for a school. Rows are created
 * once per tenant; existing (day, period) pairs are left untouched so a
 * school's adjustments survive re-seeding.
 */
class TeachingPeriodSeeder extends Seeder
{
    public function run(?string $schoolId = null): void
    {
        $school = $schoolId !== null
            ? School::query()->whereKey($schoolId)->first()
            : School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        foreach (config('nexus-timetable.teaching_periods', []) as $definition) {
            TeachingPeriod::firstOrCreate(
                [
                    'school_id' => $school->getKey(),
                    'day_of_week' => $definition['day'],
                    'period_number' => $definition['period'],
                ],
                [
                    'starts_at' => $definition['starts_at'],
                    'ends_at' => $definition['ends_at'],
                    'is_active' => true,
                ],
            );
        }
    }
}
