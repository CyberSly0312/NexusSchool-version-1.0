<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\DTOs\ReportData;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Domains\Analytics\Events\ReportGenerated;
use App\Domains\Analytics\Events\ReportScheduled;
use App\Domains\Analytics\Rules\ReportRules;
use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReportingService
{
    public function __construct(
        protected Report $model = new Report(),
    ) {
    }

    public function create(array $data): ReportData
    {
        $validated = ReportRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? 'draft';
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $report = $this->model->create($validated);

            return ReportData::create($report->toArray());
        });
    }

    public function update(Report $report, array $data): ReportData
    {
        $validated = ReportRules::validate($data, 'update', $report->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($report->school_id, $validated['name'], $report->id);
        }

        $report->update($validated);

        return ReportData::create($report->fresh()->toArray());
    }

    public function delete(Report $report): void
    {
        $report->delete();
    }

    public function schedule(Report $report, string $frequency, ?string $scheduledAt = null): ReportData
    {
        if ($report->status === ReportStatus::Generating) {
            throw ValidationException::withMessages(['status' => 'Cannot schedule a report that is currently generating.']);
        }

        $report->update([
            'status' => ReportStatus::Scheduled,
            'scheduled_frequency' => $frequency,
            'scheduled_at' => $scheduledAt ?? now(),
        ]);

        ReportScheduled::dispatch($report->fresh());

        return ReportData::create($report->fresh()->toArray());
    }

    public function markGenerating(Report $report): ReportData
    {
        $report->update(['status' => ReportStatus::Generating]);

        return ReportData::create($report->fresh()->toArray());
    }

    public function markCompleted(Report $report): ReportData
    {
        $report->update([
            'status' => ReportStatus::Completed,
            'last_generated_at' => now(),
        ]);

        ReportGenerated::dispatch($report->fresh());

        return ReportData::create($report->fresh()->toArray());
    }

    public function markFailed(Report $report): ReportData
    {
        $report->update(['status' => ReportStatus::Failed]);

        return ReportData::create($report->fresh()->toArray());
    }

    public function getScheduledReports(): Collection
    {
        return $this->model
            ->where('status', ReportStatus::Scheduled)
            ->where('scheduled_at', '<=', now())
            ->get();
    }

    public function findById(string $id): Report
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

        if (isset($filters['format'])) {
            $query->where('format', $filters['format']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
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
            throw ValidationException::withMessages(['name' => 'A report with this name already exists in this school.']);
        }
    }
}
