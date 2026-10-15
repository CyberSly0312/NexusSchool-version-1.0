<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Events\SchoolCreated;
use App\Domains\School\Events\SchoolSuspended;
use App\Domains\School\Events\SchoolUpdated;
use App\Domains\School\Models\School;
use App\Domains\School\Rules\SchoolRules;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolService
{
    public function __construct(
        protected School $model = new School(),
    ) {
    }

    /**
     * Create a new school with full tenant provisioning.
     */
    public function create(SchoolData $data): School
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();
            $slug = $this->canonicalSlug($attributes['slug'] ?? $attributes['name']);
            $domain = $this->canonicalDomain($slug);

            // A platform hostname is assigned once at provisioning time. A
            // rename does not alter it; changing domains is an explicit future
            // domain-lifecycle operation, never an incidental school update.
            if (($attributes['domain'] ?? null) !== null && $attributes['domain'] !== $domain) {
                throw ValidationException::withMessages([
                    'domain' => 'The school domain is generated from its slug and cannot be chosen separately.',
                ]);
            }

            $attributes['slug'] = $slug;
            $attributes['domain'] = $domain;
            $attributes['database'] ??= 'school_'.$slug;

            $validated = SchoolRules::validate($attributes);

            try {
                $school = $this->model->newQuery()->create($validated);
                $school->domains()->create(['domain' => $domain]);
            } catch (QueryException $exception) {
                if ($this->isUniqueConstraintViolation($exception)) {
                    throw ValidationException::withMessages([
                        'slug' => "The subdomain '{$slug}' is unavailable. Choose a different school slug.",
                    ]);
                }

                throw $exception;
            }

            SchoolCreated::dispatch($school);

            return $school;
        });
    }

    /**
     * Update an existing school.
     */
    public function update(School $school, array $data): School
    {
        // The canonical hostname is immutable through ordinary school edits.
        // Domain lifecycle changes must preserve registry ownership explicitly.
        unset($data['domain']);

        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $data['slug'] = $this->canonicalSlug($data['slug']);
        }

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
        return $this->model
            ->whereHas('domains', fn ($query) => $query->where('domain', Str::lower($domain)))
            ->firstOrFail();
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

    private function canonicalSlug(string $value): string
    {
        $slug = Str::lower(Str::slug($value));

        if ($slug === '' || strlen($slug) > 63 || ! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $slug)) {
            throw ValidationException::withMessages([
                'slug' => 'The school slug must produce a valid DNS subdomain (letters, numbers, and internal hyphens only).',
            ]);
        }

        if (in_array($slug, config('tenancy.reserved_subdomains', []), true)) {
            throw ValidationException::withMessages([
                'slug' => "The subdomain '{$slug}' is reserved by the platform.",
            ]);
        }

        return $slug;
    }

    private function canonicalDomain(string $slug): string
    {
        $baseDomain = Str::lower(trim((string) config('tenancy.tenant_base_domain')));

        if (! preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $baseDomain)) {
            throw new \LogicException('TENANT_BASE_DOMAIN must be a valid DNS domain.');
        }

        return "{$slug}.{$baseDomain}";
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505', '19'], true);
    }
}
