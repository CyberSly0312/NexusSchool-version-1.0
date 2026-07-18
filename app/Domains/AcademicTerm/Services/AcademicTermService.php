<?php

declare(strict_types=1);

namespace App\Domains\AcademicTerm\Services;

use App\Domains\AcademicTerm\DTOs\AcademicTermData;
use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use App\Domains\AcademicTerm\Events\AcademicTermActivated;
use App\Domains\AcademicTerm\Events\AcademicTermArchived;
use App\Domains\AcademicTerm\Events\AcademicTermCreated;
use App\Domains\AcademicTerm\Events\AcademicTermStatusChanged;
use App\Domains\AcademicTerm\Events\AcademicTermUpdated;
use App\Domains\AcademicTerm\Rules\AcademicTermRules;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicTermService
{
    public function __construct(
        protected AcademicTerm $model = new AcademicTerm(),
    ) {
    }

    public function create(AcademicTermData $data): AcademicTerm
    {
        return DB::transaction(function () use ($data) {
            $this->validateYearContainment($data->startDate, $data->endDate, $data->academicYearId);
            $this->validateNoOverlap($data->startDate, $data->endDate, $data->academicYearId);

            $academicTerm = $this->model->create($data->toArray());

            AcademicTermCreated::dispatch($academicTerm);

            return $academicTerm;
        });
    }

    public function update(AcademicTerm $academicTerm, array $data): AcademicTerm
    {
        $validated = AcademicTermRules::validate($data, 'update', $academicTerm->id);

        $yearId = $validated['academic_year_id'] ?? $academicTerm->academic_year_id;
        $startDate = $validated['start_date'] ?? $academicTerm->start_date?->toDateString();
        $endDate = $validated['end_date'] ?? $academicTerm->end_date?->toDateString();

        $this->validateYearContainment($startDate, $endDate, $yearId);
        $this->validateNoOverlap($startDate, $endDate, $yearId, $academicTerm->id);

        $oldStatus = $academicTerm->status;

        $academicTerm->update($validated);

        AcademicTermUpdated::dispatch($academicTerm);

        $newStatus = $academicTerm->fresh()?->status;

        if ($oldStatus !== $newStatus) {
            AcademicTermStatusChanged::dispatch(
                $academicTerm->fresh(),
                $oldStatus,
                $newStatus,
            );
        }

        return $academicTerm->refresh();
    }

    public function delete(AcademicTerm $academicTerm): bool
    {
        $academicTerm->delete();

        return true;
    }

    public function activate(AcademicTerm $academicTerm): AcademicTerm
    {
        return DB::transaction(function () use ($academicTerm) {
            $previousActive = $this->getActiveForYear($academicTerm->academic_year_id);

            if ($previousActive && $previousActive->id !== $academicTerm->id) {
                $previousActive->update(['status' => AcademicTermStatus::Draft]);

                AcademicTermStatusChanged::dispatch(
                    $previousActive->fresh(),
                    AcademicTermStatus::Active,
                    AcademicTermStatus::Draft,
                );
            }

            $oldStatus = $academicTerm->status;
            $academicTerm->update(['status' => AcademicTermStatus::Active]);

            AcademicTermActivated::dispatch($academicTerm->fresh());

            AcademicTermStatusChanged::dispatch(
                $academicTerm->fresh(),
                $oldStatus,
                AcademicTermStatus::Active,
            );

            return $academicTerm->refresh();
        });
    }

    public function archive(AcademicTerm $academicTerm): AcademicTerm
    {
        $oldStatus = $academicTerm->status;

        $academicTerm->update(['status' => AcademicTermStatus::Archived]);

        AcademicTermArchived::dispatch($academicTerm->fresh());

        AcademicTermStatusChanged::dispatch(
            $academicTerm->fresh(),
            $oldStatus,
            AcademicTermStatus::Archived,
        );

        return $academicTerm->refresh();
    }

    public function getActiveForYear(int $academicYearId): ?AcademicTerm
    {
        return $this->model
            ->where('academic_year_id', $academicYearId)
            ->where('status', AcademicTermStatus::Active)
            ->first();
    }

    public function hasActiveTerm(int $academicYearId): bool
    {
        return $this->getActiveForYear($academicYearId) !== null;
    }

    public function findById(int $id): AcademicTerm
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

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    private function validateYearContainment(string $startDate, string $endDate, int $academicYearId): void
    {
        $year = AcademicYear::findOrFail($academicYearId);

        $termStart = Carbon::parse($startDate);
        $termEnd = Carbon::parse($endDate);
        $yearStart = $year->start_date;
        $yearEnd = $year->end_date;

        if ($termStart->lt($yearStart) || $termEnd->gt($yearEnd)) {
            throw ValidationException::withMessages([
                'start_date' => "Term dates must be within the academic year ({$yearStart->format('Y-m-d')} to {$yearEnd->format('Y-m-d')}).",
            ]);
        }
    }

    private function validateNoOverlap(string $startDate, string $endDate, int $academicYearId, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('academic_year_id', $academicYearId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_date' => 'The term date range overlaps with an existing term.',
            ]);
        }
    }
}
