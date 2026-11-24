<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserInvitationFactory extends Factory
{
    protected $model = UserInvitation::class;

    public function definition(): array
    {
        $plainToken = bin2hex(random_bytes(32));

        return [
            'user_id' => User::factory(),
            'school_id' => function (array $attributes): string {
                $user = User::query()->find($attributes['user_id']);

                return is_string($user?->school_id) && $user->school_id !== ''
                    ? $user->school_id
                    : (string) Str::ulid();
            },
            'purpose' => UserInvitation::PURPOSE_STAFF_INITIAL,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addHours(UserInvitation::EXPIRATION_HOURS),
            'consumed_at' => null,
            'revoked_at' => null,
            'invited_by_user_id' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subMinute(),
        ]);
    }

    public function consumed(): static
    {
        return $this->state(fn (): array => [
            'consumed_at' => now(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (): array => [
            'revoked_at' => now(),
        ]);
    }
}
