<?php

namespace App\Domains\AcademicYear\Services;

use App\Domains\AcademicYear\DTOs\AcademicYearData;
use App\Domains\AcademicYear\Enums\AcademicYearStatus;
use App\Domains\AcademicYear\Events\AcademicYearActivated;
use App\Domains\AcademicYear\Events\AcademicYearArchived;
use App\Domains\AcademicYear\Events\AcademicYearCloned;
use App\Domains\AcademicYear\Events\AcademicYearCreated;
use App\Domains\AcademicYear\Events\AcademicYearDatesChanged;
use App\Domains\AcademicYear\Events\AcademicYearDeleted;
use App\Domains\AcademicYear\Events\AcademicYearStatusChanged;
use App\Domains\AcademicYear\Events\AcademicYearSwitched;
use App\Domains\AcademicYear\Events\AcademicYearUpdated;
use App\Domains\AcademicYear\Rules\AcademicYearRules;
use App\Models\AcademicYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AcademicYearService
{
    public function __construct(
        protected AcademicYear $model = new AcademicYear,
    ) {}

    public function create(AcademicYearData $data): AcademicYear
    {
        return DB::transaction(function () use ($data) {
            $academicYear = $this->model->create($data->toArray());

            AcademicYearCreated::dispatch($academicYear);

            return $academicYear;
        });
    }

    public function update(AcademicYear $academicYear, array $data): AcademicYear
    {
        $validated = AcademicYearRules::validate($data, 'update', $academicYear->id);

        $oldStartDate = $academicYear->start_date?->toDateString();
        $oldEndDate = $academicYear->end_date?->toDateString();

        $oldStatus = $academicYear->status;

        $academicYear->update($validated);

        AcademicYearUpdated::dispatch($academicYear);

        $newStartDate = $academicYear->fresh()?->start_date?->toDateString();
        $newEndDate = $academicYear->fresh()?->end_date?->toDateString();

        if ($oldStartDate !== $newStartDate || $oldEndDate !== $newEndDate) {
            AcademicYearDatesChanged::dispatch(
                $academicYear->fresh(),
                $oldStartDate,
                $oldEndDate,
            );
        }

        $newStatus = $academicYear->fresh()?->status;

        if ($oldStatus !== $newStatus) {
            AcademicYearStatusChanged::dispatch(
                $academicYear->fresh(),
                $oldStatus,
                $newStatus,
            );
        }

        return $academicYear->refresh();
    }

    public function delete(AcademicYear $academicYear): bool
    {
        $academicYear->delete();

        AcademicYearDeleted::dispatch($academicYear);

        return true;
    }

    public function activate(AcademicYear $academicYear): AcademicYear
    {
        return DB::transaction(function () use ($academicYear) {
            $previousActive = $this->getActiveForSchool($academicYear->school_id);

            if ($previousActive && $previousActive->id !== $academicYear->id) {
                $previousActive->update(['status' => AcademicYearStatus::Draft]);

                AcademicYearStatusChanged::dispatch(
                    $previousActive->fresh(),
                    AcademicYearStatus::Active,
                    AcademicYearStatus::Draft,
                );
            }

            $oldStatus = $academicYear->status;
            $academicYear->update(['status' => AcademicYearStatus::Active]);

            AcademicYearActivated::dispatch($academicYear->fresh());

            AcademicYearStatusChanged::dispatch(
                $academicYear->fresh(),
                $oldStatus,
                AcademicYearStatus::Active,
            );

            AcademicYearSwitched::dispatch(
                $academicYear->school,
                $previousActive,
                $academicYear->fresh(),
            );

            return $academicYear->refresh();
        });
    }

    public function archive(AcademicYear $academicYear): AcademicYear
    {
        $oldStatus = $academicYear->status;

        $academicYear->update(['status' => AcademicYearStatus::Archived]);

        AcademicYearArchived::dispatch($academicYear->fresh());

        AcademicYearStatusChanged::dispatch(
            $academicYear->fresh(),
            $oldStatus,
            AcademicYearStatus::Archived,
        );

        return $academicYear->refresh();
    }

    public function clone(AcademicYear $source, array $overrides = []): AcademicYear
    {
        return DB::transaction(function () use ($source, $overrides) {
            $data = [
                'school_id' => $overrides['school_id'] ?? $source->school_id,
                'name' => $overrides['name'] ?? $source->name.' (Copy)',
                'code' => $overrides['code'] ?? $source->code,
                'start_date' => $overrides['start_date'] ?? $source->start_date->toDateString(),
                'end_date' => $overrides['end_date'] ?? $source->end_date->toDateString(),
                'description' => $overrides['description'] ?? $source->description,
                'status' => $overrides['status'] ?? AcademicYearStatus::Draft->value,
            ];

            $clone = $this->model->create($data);

            AcademicYearCloned::dispatch($source, $clone);

            return $clone;
        });
    }

    public function getActiveForSchool(string|int $schoolId): ?AcademicYear
    {
        return $this->model
            ->where('school_id', $schoolId)
            ->where('status', AcademicYearStatus::Active)
            ->first();
    }

    public function hasActiveYear(string|int $schoolId): bool
    {
        return $this->getActiveForSchool($schoolId) !== null;
    }

    public function getForSchool(string|int $schoolId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('school_id', $schoolId);

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

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    public function findById(int $id): AcademicYear
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

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }
}
