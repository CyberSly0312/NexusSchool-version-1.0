<?php

namespace App\Domains\Program\Services;

use App\Domains\Program\DTOs\ProgramData;
use App\Domains\Program\Enums\ProgramStatus;
use App\Domains\Program\Events\ProgramArchived;
use App\Domains\Program\Events\ProgramCreated;
use App\Domains\Program\Events\ProgramUpdated;
use App\Domains\Program\Rules\ProgramRules;
use App\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgramService
{
    public function __construct(
        protected Program $model = new Program,
    ) {}

    public function create(ProgramData $data): Program
    {
        $validated = ProgramRules::validate($data->toArray(), 'create');

        $this->validateUniquePerSchool($validated['school_id'], $validated['name'], $validated['code']);

        return DB::transaction(function () use ($validated) {
            $program = $this->model->create($validated);

            ProgramCreated::dispatch($program);

            return $program;
        });
    }

    public function update(Program $program, array $data): Program
    {
        $validated = ProgramRules::validate($data, 'update', $program->id);

        $schoolId = $validated['school_id'] ?? $program->school_id;
        $name = $validated['name'] ?? $program->name;
        $code = $validated['code'] ?? $program->code;

        $this->validateUniquePerSchool($schoolId, $name, $code, $program->id);

        $program->update($validated);

        ProgramUpdated::dispatch($program->refresh());

        return $program->refresh();
    }

    public function delete(Program $program): bool
    {
        $program->delete();

        return true;
    }

    public function archive(Program $program): Program
    {
        $program->update(['status' => ProgramStatus::Archived]);

        ProgramArchived::dispatch($program->fresh());

        return $program->refresh();
    }

    public function restore(Program $program): Program
    {
        $program->update(['status' => ProgramStatus::Active]);

        ProgramUpdated::dispatch($program->refresh());

        return $program->refresh();
    }

    public function findById(int $id): Program
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

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->orderBy('order')->orderBy('name')->paginate($perPage);
    }

    private function validateUniquePerSchool(string $schoolId, string $name, string $code, ?int $excludeId = null): void
    {
        $nameQuery = $this->model
            ->where('school_id', $schoolId)
            ->where('name', $name);

        $codeQuery = $this->model
            ->where('school_id', $schoolId)
            ->where('code', $code);

        if ($excludeId) {
            $nameQuery->where('id', '!=', $excludeId);
            $codeQuery->where('id', '!=', $excludeId);
        }

        if ($nameQuery->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A program with this name already exists in this school.',
            ]);
        }

        if ($codeQuery->exists()) {
            throw ValidationException::withMessages([
                'code' => 'A program with this code already exists in this school.',
            ]);
        }
    }
}
