<?php
declare(strict_types=1);

namespace App\Domains\Plugin\DTOs;

final readonly class PluginSettingData
{
    public function __construct(
        public string $key,
        public string $label,
        public string $type = 'text',
        public mixed $default = null,
        public ?string $description = null,
        public bool $required = false,
        public array $options = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'] ?? '',
            label: $data['label'] ?? $data['key'] ?? '',
            type: $data['type'] ?? 'text',
            default: $data['default'] ?? null,
            description: $data['description'] ?? null,
            required: $data['required'] ?? false,
            options: $data['options'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type,
            'default' => $this->default,
            'description' => $this->description,
            'required' => $this->required,
            'options' => $this->options,
        ];
    }
}
