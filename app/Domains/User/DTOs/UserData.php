<?php

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\UserStatus;

readonly class UserData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $password = null,
        public ?string $phone = null,
        public ?string $avatar = null,
        public ?string $schoolId = null,
        public UserStatus $status = UserStatus::Pending,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            phone: $data['phone'] ?? null,
            avatar: $data['avatar'] ?? null,
            schoolId: $data['school_id'] ?? null,
            status: isset($data['status'])
                ? UserStatus::from($data['status'])
                : UserStatus::Pending,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'school_id' => $this->schoolId,
            'status' => $this->status->value,
        ];
    }
}
