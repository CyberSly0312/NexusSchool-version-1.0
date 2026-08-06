<?php

declare(strict_types=1);

namespace Database\Seeders\Staff;

use App\Domains\School\Models\School;
use App\Domains\Staff\Infrastructure\Models\Position;
use Illuminate\Database\Seeder;

/**
 * Materializes the config-driven positions catalog for a school. Rows are
 * created once per tenant; existing codes are left untouched so a school's
 * customizations survive re-seeding. Mirrors the SPR-005 RelationshipTypeSeeder.
 */
class PositionSeeder extends Seeder
{
    public function run(?string $schoolId = null): void
    {
        $school = $schoolId !== null
            ? School::query()->whereKey($schoolId)->first()
            : School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        foreach (config('nexus-staff.positions', []) as $definition) {
            Position::firstOrCreate(
                ['school_id' => $school->getKey(), 'code' => $definition['code']],
                [
                    'title' => $definition['title'],
                    'category' => $definition['category']->value,
                    'sort_order' => $definition['sort_order'] ?? 0,
                    'is_active' => true,
                ],
            );
        }
    }
}
