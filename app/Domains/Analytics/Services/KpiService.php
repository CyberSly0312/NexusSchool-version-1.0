<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\DTOs\KpiData;
use App\Domains\Analytics\Events\KpiRecalculated;
use App\Domains\Analytics\Rules\KpiRules;
use App\Models\Kpi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KpiService
{
    public function __construct(
        protected Kpi $model = new Kpi(),
    ) {
    }

    public function create(array $data): KpiData
    {
        $validated = KpiRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? 'active';
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $kpi = $this->model->create($validated);

            return KpiData::create($kpi->toArray());
        });
    }

    public function update(Kpi $kpi, array $data): KpiData
    {
        $validated = KpiRules::validate($data, 'update', $kpi->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($kpi->school_id, $validated['name'], $kpi->id);
        }

        $kpi->update($validated);

        return KpiData::create($kpi->fresh()->toArray());
    }

    public function delete(Kpi $kpi): void
    {
        $kpi->delete();
    }

    public function archive(Kpi $kpi): KpiData
    {
        $kpi->update(['status' => 'archived']);

        return KpiData::create($kpi->fresh()->toArray());
    }

    public function activate(Kpi $kpi): KpiData
    {
        $kpi->update(['status' => 'active']);

        return KpiData::create($kpi->fresh()->toArray());
    }

    public function calculate(Kpi $kpi, ?float $value = null): KpiData
    {
        if ($value === null) {
            $value = $this->computeValue($kpi);
        }

        $kpi->update([
            'cached_value' => $value,
            'cached_at' => now(),
            'last_calculated_at' => now(),
        ]);

        $cacheKey = "kpi:{$kpi->school_id}:{$kpi->slug}";
        Cache::put($cacheKey, $value, 3600);

        KpiRecalculated::dispatch($kpi->fresh());

        return KpiData::create($kpi->fresh()->toArray());
    }

    public function getCachedValue(Kpi $kpi): ?float
    {
        if ($kpi->isCacheValid()) {
            return (float) $kpi->cached_value;
        }

        $cacheKey = "kpi:{$kpi->school_id}:{$kpi->slug}";

        return Cache::get($cacheKey);
    }

    public function calculateAllForSchool(string $schoolId): int
    {
        $kpis = $this->model->where('school_id', $schoolId)->where('status', 'active')->get();
        $count = 0;

        foreach ($kpis as $kpi) {
            $this->calculate($kpi);
            $count++;
        }

        return $count;
    }

    public function findById(string $id): Kpi
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    private function computeValue(Kpi $kpi): float
    {
        $formula = $kpi->formula;

        if (! $formula || ! isset($formula['type'])) {
            return 0.0;
        }

        return match ($formula['type']) {
            'count' => $this->computeCountFormula($kpi->school_id, $formula),
            'sum' => $this->computeSumFormula($kpi->school_id, $formula),
            'average' => $this->computeAverageFormula($kpi->school_id, $formula),
            default => 0.0,
        };
    }

    private function computeCountFormula(string $schoolId, array $formula): float
    {
        $table = $formula['table'] ?? null;
        $column = $formula['column'] ?? null;
        $conditions = $formula['conditions'] ?? [];

        if (! $table) {
            return 0.0;
        }

        $query = DB::table($table)->where('school_id', $schoolId);

        foreach ($conditions as $condition) {
            $query->where($condition['column'] ?? '', $condition['operator'] ?? '=', $condition['value'] ?? '');
        }

        return (float) $query->count();
    }

    private function computeSumFormula(string $schoolId, array $formula): float
    {
        $table = $formula['table'] ?? null;
        $column = $formula['column'] ?? null;

        if (! $table || ! $column) {
            return 0.0;
        }

        return (float) DB::table($table)->where('school_id', $schoolId)->sum($column);
    }

    private function computeAverageFormula(string $schoolId, array $formula): float
    {
        $table = $formula['table'] ?? null;
        $column = $formula['column'] ?? null;

        if (! $table || ! $column) {
            return 0.0;
        }

        return (float) DB::table($table)->where('school_id', $schoolId)->avg($column);
    }

    private function validateUniqueSlugPerSchool(string $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'A KPI with this name already exists in this school.']);
        }
    }
}
