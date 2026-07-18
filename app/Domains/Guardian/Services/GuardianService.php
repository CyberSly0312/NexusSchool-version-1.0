<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Services;

use App\Domains\Guardian\DTOs\GuardianData;
use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Events\GuardianActivated;
use App\Domains\Guardian\Events\GuardianArchived;
use App\Domains\Guardian\Events\GuardianCreated;
use App\Domains\Guardian\Events\GuardianUpdated;
use App\Domains\Guardian\Rules\GuardianRules;
use App\Models\Guardian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuardianService
{
    public function __construct(
        protected Guardian $model = new Guardian(),
    ) {
    }

    public function create(GuardianData $data): Guardian
    {
        $validated = GuardianRules::validate($data->toArray(), 'create');

        $this->validateUniqueGuardianNumberPerSchool($validated['school_id'], $validated['guardian_number']);
        $this->validateUniquePhonePerSchool($validated['school_id'], $validated['phone_number']);

        if (! empty($validated['email'])) {
            $this->validateUniqueEmailPerSchool($validated['school_id'], $validated['email']);
        }

        if (! empty($validated['user_id'])) {
            $this->validateUserNotLinked($validated['user_id']);
        }

        return DB::transaction(function () use ($validated) {
            $guardian = $this->model->create($validated);

            GuardianCreated::dispatch($guardian);

            return $guardian;
        });
    }

    public function update(Guardian $guardian, array $data): Guardian
    {
        $validated = GuardianRules::validate($data, 'update', $guardian->id);

        $schoolId = $validated['school_id'] ?? $guardian->school_id;

        if (isset($validated['guardian_number'])) {
            $this->validateUniqueGuardianNumberPerSchool($schoolId, $validated['guardian_number'], $guardian->id);
        }

        if (isset($validated['phone_number'])) {
            $this->validateUniquePhonePerSchool($schoolId, $validated['phone_number'], $guardian->id);
        }

        $email = $validated['email'] ?? $guardian->email;
        if (! empty($email)) {
            $this->validateUniqueEmailPerSchool($schoolId, $email, $guardian->id);
        }

        if (isset($validated['user_id'])) {
            $this->validateUserNotLinked($validated['user_id'], $guardian->id);
        }

        $guardian->update($validated);

        GuardianUpdated::dispatch($guardian->refresh());

        return $guardian->refresh();
    }

    public function delete(Guardian $guardian): bool
    {
        $guardian->delete();

        return true;
    }

    public function archive(Guardian $guardian): Guardian
    {
        $guardian->update(['status' => GuardianStatus::Archived]);

        GuardianArchived::dispatch($guardian->fresh());

        return $guardian->refresh();
    }

    public function activate(Guardian $guardian): Guardian
    {
        $guardian->update(['status' => GuardianStatus::Active]);

        GuardianActivated::dispatch($guardian->fresh());

        return $guardian->refresh();
    }

    public function findById(int $id): Guardian
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('guardian_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['relationship_type'])) {
            $query->where('relationship_type', $filters['relationship_type']);
        }

        if (isset($filters['is_primary_guardian'])) {
            $query->where('is_primary_guardian', $filters['is_primary_guardian']);
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage);
    }

    private function validateUniqueGuardianNumberPerSchool(string $schoolId, string $guardianNumber, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('guardian_number', $guardianNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'guardian_number' => 'A guardian with this number already exists in this school.',
            ]);
        }
    }

    private function validateUniquePhonePerSchool(string $schoolId, string $phoneNumber, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('phone_number', $phoneNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'phone_number' => 'A guardian with this phone number already exists in this school.',
            ]);
        }
    }

    private function validateUniqueEmailPerSchool(string $schoolId, string $email, ?int $excludeId = null): void
    {
        $query = $this->model
            ->where('school_id', $schoolId)
            ->where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A guardian with this email already exists in this school.',
            ]);
        }
    }

    private function validateUserNotLinked(int $userId, ?int $excludeGuardianId = null): void
    {
        $query = $this->model->where('user_id', $userId);

        if ($excludeGuardianId) {
            $query->where('id', '!=', $excludeGuardianId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'This user is already linked to another guardian.',
            ]);
        }
    }
}
