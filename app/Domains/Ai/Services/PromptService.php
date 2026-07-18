<?php

declare(strict_types=1);

namespace App\Domains\Ai\Services;

use App\Domains\Ai\DTOs\AiPromptTemplateData;
use App\Domains\Ai\Events\PromptTemplateUpdated;
use App\Domains\Ai\Rules\AiPromptTemplateRules;
use App\Models\AiPromptTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PromptService
{
    public function __construct(
        protected AiPromptTemplate $model = new AiPromptTemplate(),
    ) {
    }

    public function create(array $data): AiPromptTemplateData
    {
        $validated = AiPromptTemplateRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['version'] = 1;
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $template = $this->model->create($validated);

            return AiPromptTemplateData::create($template->toArray());
        });
    }

    public function update(AiPromptTemplate $template, array $data): AiPromptTemplateData
    {
        $validated = AiPromptTemplateRules::validate($data, 'update', $template->id);
        $previousVersion = $template->version;

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($template->school_id, $validated['name'], $template->id);
        }

        $changedFields = array_intersect_key($validated, array_flip([
            'system_prompt', 'user_prompt_template', 'category', 'variables',
        ]));

        if (! empty($changedFields)) {
            $validated['version'] = $template->version + 1;
        }

        $template->update($validated);

        if (! empty($changedFields)) {
            PromptTemplateUpdated::dispatch($template->fresh(), $previousVersion);
        }

        return AiPromptTemplateData::create($template->fresh()->toArray());
    }

    public function delete(AiPromptTemplate $template): void
    {
        $template->delete();
    }

    public function archive(AiPromptTemplate $template): AiPromptTemplateData
    {
        $template->update(['status' => 'archived']);

        return AiPromptTemplateData::create($template->fresh()->toArray());
    }

    public function activate(AiPromptTemplate $template): AiPromptTemplateData
    {
        $template->update(['status' => 'active']);

        return AiPromptTemplateData::create($template->fresh()->toArray());
    }

    public function findById(string $id): AiPromptTemplate
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug(string $schoolId, string $slug): AiPromptTemplate
    {
        return $this->model->where('school_id', $schoolId)->where('slug', $slug)->firstOrFail();
    }

    public function getActiveForCategory(string $schoolId, string $category): ?AiPromptTemplate
    {
        return $this->model
            ->where('school_id', $schoolId)
            ->where('category', $category)
            ->where('status', 'active')
            ->latest('version')
            ->first();
    }

    public function render(string $templateId, array $variables = []): string
    {
        $template = $this->findById($templateId);

        return $template->render($variables);
    }

    public function getVersions(string $schoolId, string $slug): Collection
    {
        return $this->model
            ->where('school_id', $schoolId)
            ->where('slug', $slug)
            ->orderBy('version', 'desc')
            ->get();
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

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    private function validateUniqueSlugPerSchool(string $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'A prompt template with this name already exists in this school.']);
        }
    }
}
