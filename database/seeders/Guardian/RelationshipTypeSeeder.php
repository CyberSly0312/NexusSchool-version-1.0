<?php

declare(strict_types=1);

namespace Database\Seeders\Guardian;

use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;

/**
 * Materializes the config-driven relationship type catalog for a school.
 * Rows are created once per tenant; existing codes are left untouched so a
 * school's customizations survive re-seeding.
 */
class RelationshipTypeSeeder extends Seeder
{
    public function run(?string $schoolId = null): void
    {
        $school = $schoolId !== null
            ? School::query()->whereKey($schoolId)->first()
            : School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        foreach (config('nexus-guardian.relationship_types', []) as $definition) {
            RelationshipType::firstOrCreate(
                ['school_id' => $school->getKey(), 'code' => $definition['code']],
                [
                    'label' => $definition['label'],
                    'description' => $definition['description'] ?? null,
                    'category' => $definition['category']->value,
                    'sort_order' => $definition['sort_order'] ?? 0,
                    'is_active' => true,
                ],
            );
        }
    }
}
