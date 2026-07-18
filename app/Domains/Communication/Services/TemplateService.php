<?php

declare(strict_types=1);

namespace App\Domains\Communication\Services;

use App\Domains\Communication\DTOs\NotificationTemplateData;
use App\Domains\Communication\Rules\NotificationTemplateRules;
use App\Models\NotificationTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TemplateService
{
    public function __construct(
        protected NotificationTemplateRules $templateRules,
    ) {
    }

    public function create(mixed $data): NotificationTemplateData
    {
        $validated = $this->templateRules->validate($data, 'create');

        if (! isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $validated['is_active'] ?? true;

        $template = DB::transaction(function () use ($validated) {
            return NotificationTemplate::create($validated);
        });

        return NotificationTemplateData::create($template->toArray());
    }

    public function update(string $id, mixed $data): NotificationTemplateData
    {
        $template = NotificationTemplate::findOrFail($id);

        $validated = $this->templateRules->validate($data, 'update');

        $template = DB::transaction(function () use ($template, $validated) {
            $template->update($validated);

            return $template;
        });

        return NotificationTemplateData::create($template->fresh()->toArray());
    }

    public function delete(string $id): void
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->delete();
    }

    public function activate(string $id): NotificationTemplateData
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->update(['is_active' => true]);

        return NotificationTemplateData::create($template->fresh()->toArray());
    }

    public function deactivate(string $id): NotificationTemplateData
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->update(['is_active' => false]);

        return NotificationTemplateData::create($template->fresh()->toArray());
    }

    public function render(string $slug, array $variables = [], ?string $schoolId = null): ?array
    {
        $query = NotificationTemplate::where('slug', $slug)->where('is_active', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $template = $query->first();

        if (! $template) {
            return null;
        }

        return [
            'subject' => $template->renderSubject($variables),
            'body' => $template->render($variables),
            'channel' => $template->channel->value,
        ];
    }

    public function getByCategory(string $category, ?string $schoolId = null): Collection
    {
        $query = NotificationTemplate::where('category', $category)->where('is_active', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->get();
    }

    public function getAll(?string $schoolId = null): Collection
    {
        $query = NotificationTemplate::query();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->latest()->get();
    }

    public function getBySlug(string $slug, ?string $schoolId = null): ?NotificationTemplate
    {
        $query = NotificationTemplate::where('slug', $slug);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->first();
    }
}
