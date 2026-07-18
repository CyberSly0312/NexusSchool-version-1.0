<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\UserData;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Events\UserCreated;
use App\Domains\User\Events\UserUpdated;
use App\Domains\User\Rules\UserRules;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected User $model = new User(),
    ) {
    }

    public function create(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $userData = $data->toArray();

            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            } else {
                unset($userData['password']);
            }

            $user = $this->model->create($userData);

            UserCreated::dispatch($user);

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        $validated = UserRules::validate($data, 'update', (string) $user->getRouteKey());

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        UserUpdated::dispatch($user);

        return $user->refresh();
    }

    public function toggleStatus(User $user): User
    {
        $newStatus = $user->isActive()
            ? UserStatus::Inactive
            : UserStatus::Active;

        $user->update(['status' => $newStatus]);

        UserUpdated::dispatch($user);

        return $user->refresh();
    }

    public function recordLogin(User $user): void
    {
        $user->update(['last_login_at' => now()]);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): User
    {
        $user->restore();

        UserUpdated::dispatch($user);

        return $user->refresh();
    }

    public function findById(string $id): User
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
