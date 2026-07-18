<?php

declare(strict_types=1);

namespace App\Domains\GradeLevel\Services;

use App\Domains\GradeLevel\DTOs\GradeLevelData;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use App\Domains\GradeLevel\Events\GradeLevelArchived;
use App\Domains\GradeLevel\Events\GradeLevelCreated;
use App\Domains\GradeLevel\Events\GradeLevelUpdated;
use App\Domains\GradeLevel\Rules\GradeLevelRules;
use App\Models\GradeLevel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeLevelService
{
    public function __construct(
        protected GradeLevel $model = new GradeLevel(),
    ) {
    }

    public function create(GradeLevelData $data): GradeLevel
    {
        $validated = GradeLevelRules::validate($data->toArray(), 'create');

        $this->validateUniquePerProgram($validated['program_id'], $validated['name'], $validated['code'], $validated['level_order']);

        return DB::transaction(function () use ($validated) {
            $gradeLevel = $this->model->create($validated);

            GradeLevelCreated::dispatch($gradeLevel);

            return $gradeLevel;
        });
    }

    public function update(GradeLevel $gradeLevel, array $data): GradeLevel
    {
        $validated = GradeLevelRules::validate($data, 'update', $gradeLevel->id);

        $programId = $validated['program_id'] ?? $gradeLevel->program_id;
        $name = $validated['name'] ?? $gradeLevel->name;
        $code = $validated['code'] ?? $gradeLevel->code;
        $levelOrder = $validated['level_order'] ?? $gradeLevel->level_order;

        $this->validateUniquePerProgram($programId, $name, $code, $levelOrder, $gradeLevel->id);

        $gradeLevel->update($validated);

        GradeLevelUpdated::dispatch($gradeLevel->refresh());

        return $gradeLevel->refresh();
    }

    public function delete(GradeLevel $gradeLevel): bool
    {
        $gradeLevel->delete();

        return true;
    }

    public function archive(GradeLevel $gradeLevel): GradeLevel
    {
        $gradeLevel->update(['status' => GradeLevelStatus::Archived]);

        GradeLevelArchived::dispatch($gradeLevel->fresh());

        return $gradeLevel->refresh();
    }

    public function restore(GradeLevel $gradeLevel): GradeLevel
    {
        $gradeLevel->update(['status' => GradeLevelStatus::Active]);

        GradeLevelUpdated::dispatch($gradeLevel->refresh());

        return $gradeLevel->refresh();
    }

    public function findById(int $id): GradeLevel
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

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        return $query->orderBy('level_order')->paginate($perPage);
    }

    private function validateUniquePerProgram(int $programId, string $name, string $code, int $levelOrder, ?int $excludeId = null): void
    {
        $nameQuery = $this->model
            ->where('program_id', $programId)
            ->where('name', $name);

        $codeQuery = $this->model
            ->where('program_id', $programId)
            ->where('code', $code);

        $orderQuery = $this->model
            ->where('program_id', $programId)
            ->where('level_order', $levelOrder);

        if ($excludeId) {
            $nameQuery->where('id', '!=', $excludeId);
            $codeQuery->where('id', '!=', $excludeId);
            $orderQuery->where('id', '!=', $excludeId);
        }

        if ($nameQuery->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A grade level with this name already exists in this program.',
            ]);
        }

        if ($codeQuery->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A grade level with this code already exists in this program.',
            ]);
        }

        if ($orderQuery->exists()) {
            throw ValidationException::withMessages([
                'level_order' => 'A grade level with this order already exists in this program.',
            ]);
        }
    }
}
