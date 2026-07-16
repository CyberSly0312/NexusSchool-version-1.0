<?php

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Events\SchoolCreated;
use App\Domains\School\Events\SchoolSuspended;
use App\Domains\School\Events\SchoolUpdated;
use App\Domains\School\Models\School;
use App\Domains\School\Rules\SchoolRules;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SchoolService
{
    public function __construct(
        protected School $model = new School,
    ) {}

    /**
     * Create a new school with full tenant provisioning.
     */
    public function create(SchoolData $data): School
    {
        return DB::transaction(function () use ($data) {
            $school = $this->model->create($data->toArray());

            SchoolCreated::dispatch($school);

            return $school;
        });
    }

    /**
     * Update an existing school.
     */
    public function update(School $school, array $data): School
    {
        $validated = SchoolRules::validate(
            $data,
            'update',
            $school->getRouteKey(),
        );

        $school->update($validated);

        SchoolUpdated::dispatch($school);

        return $school->refresh();
    }

    /**
     * Suspend a school and reason.
     */
    public function suspend(School $school, ?string $reason = null): School
    {
        $school->update(['status' => SchoolStatus::Suspended]);

        SchoolSuspended::dispatch($school, $reason);

        return $school->refresh();
    }

    /**
     * Reactivate a suspended or inactive school.
     */
    public function reactivate(School $school): School
    {
        $school->update(['status' => SchoolStatus::Active]);

        SchoolUpdated::dispatch($school);

        return $school->refresh();
    }

    /**
     * Soft-delete a school.
     */
    public function delete(School $school): bool
    {
        return $school->delete();
    }

    /**
     * Restore a soft-deleted school.
     */
    public function restore(School $school): School
    {
        $school->restore();

        SchoolUpdated::dispatch($school);

        return $school->refresh();
    }

    /**
     * Find a school by its primary key.
     */
    public function findById(string $id): School
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find a school by slug.
     */
    public function findBySlug(string $slug): School
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    /**
     * Find a school by domain.
     */
    public function findByDomain(string $domain): School
    {
        return $this->model->where('domain', $domain)->firstOrFail();
    }

    /**
     * Paginate schools with optional filters.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_type'])) {
            $query->where('school_type', $filters['school_type']);
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }
}
