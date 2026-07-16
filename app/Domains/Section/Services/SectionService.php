<?php

namespace App\Domains\Section\Services;

use App\Domains\Section\DTOs\SectionData;
use App\Domains\Section\Enums\SectionStatus;
use App\Domains\Section\Events\SectionArchived;
use App\Domains\Section\Events\SectionCreated;
use App\Domains\Section\Events\SectionUpdated;
use App\Domains\Section\Rules\SectionRules;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SectionService
{
    public function __construct(
        protected Section $model = new Section,
    ) {}

    public function create(SectionData $data): Section
    {
        $validated = SectionRules::validate($data->toArray(), 'create');

        $this->validateUniquePerGradeLevel($validated['grade_level_id'], $validated['name'], $validated['code']);

        return DB::transaction(function () use ($validated) {
            $section = $this->model->create($validated);

            SectionCreated::dispatch($section);

            return $section;
        });
    }

    public function update(Section $section, array $data): Section
    {
        $validated = SectionRules::validate($data, 'update', $section->id);

        $gradeLevelId = $validated['grade_level_id'] ?? $section->grade_level_id;
        $name = $validated['name'] ?? $section->name;
        $code = $validated['code'] ?? $section->code;

        $this->validateUniquePerGradeLevel($gradeLevelId, $name, $code, $section->id);

        $section->update($validated);

        SectionUpdated::dispatch($section->refresh());

        return $section->refresh();
    }

    public function delete(Section $section): bool
    {
        $section->delete();

        return true;
    }

    public function archive(Section $section): Section
    {
        $section->update(['status' => SectionStatus::Archived]);

        SectionArchived::dispatch($section->fresh());

        return $section->refresh();
    }

    public function restore(Section $section): Section
    {
        $section->update(['status' => SectionStatus::Active]);

        SectionUpdated::dispatch($section->refresh());

        return $section->refresh();
    }

    public function findById(int $id): Section
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
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['grade_level_id'])) {
            $query->where('grade_level_id', $filters['grade_level_id']);
        }

        return $query->orderBy('order')->orderBy('name')->paginate($perPage);
    }

    private function validateUniquePerGradeLevel(int $gradeLevelId, string $name, string $code, ?int $excludeId = null): void
    {
        $nameQuery = $this->model
            ->where('grade_level_id', $gradeLevelId)
            ->where('name', $name);

        $codeQuery = $this->model
            ->where('grade_level_id', $gradeLevelId)
            ->where('code', $code);

        if ($excludeId) {
            $nameQuery->where('id', '!=', $excludeId);
            $codeQuery->where('id', '!=', $excludeId);
        }

        if ($nameQuery->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A section with this name already exists in this grade level.',
            ]);
        }

        if ($codeQuery->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A section with this code already exists in this grade level.',
            ]);
        }
    }
}
