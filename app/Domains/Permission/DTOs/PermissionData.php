<?php

namespace App\Domains\Permission\DTOs;

readonly class PermissionData
{
    public function __construct(
        public string $name,
        public string $guardName = 'web',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            guardName: $data['guard_name'] ?? 'web',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guardName,
        ];
    }
}
