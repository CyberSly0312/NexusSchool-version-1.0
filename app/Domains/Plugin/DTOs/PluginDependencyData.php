<?php
declare(strict_types=1);

namespace App\Domains\Plugin\DTOs;

final readonly class PluginDependencyData
{
    public function __construct(
        public string $name,
        public string $version = '*',
        public bool $required = true,
    ) {}

    public static function fromArray(array $data): self
    {
        if (is_string($data)) {
            return new self(name: $data);
        }

        return new self(
            name: $data['name'] ?? '',
            version: $data['version'] ?? '*',
            required: $data['required'] ?? true,
        );
    }
}
