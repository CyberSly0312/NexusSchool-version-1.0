<?php
declare(strict_types=1);

namespace App\Domains\Plugin\DTOs;

final readonly class PluginManifest
{
    public function __construct(
        public string $name,
        public string $displayName,
        public string $description,
        public string $version,
        public string $minimumNexusVersion,
        public string $author,
        public string $license,
        public array $dependencies = [],
        public array $permissions = [],
        public array $settings = [],
        public array $menus = [],
        public array $routes = [],
        public array $migrations = [],
        public array $seeders = [],
        public array $translations = [],
        public array $assets = [],
        public array $providers = [],
        public ?PluginFilamentConfig $filament = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            displayName: $data['display_name'] ?? $data['name'] ?? '',
            description: $data['description'] ?? '',
            version: $data['version'] ?? '1.0.0',
            minimumNexusVersion: $data['minimum_nexus_version'] ?? '1.0.0',
            author: $data['author'] ?? '',
            license: $data['license'] ?? 'MIT',
            dependencies: $data['dependencies'] ?? [],
            permissions: $data['permissions'] ?? [],
            settings: $data['settings'] ?? [],
            menus: $data['menus'] ?? [],
            routes: $data['routes'] ?? [],
            migrations: $data['migrations'] ?? [],
            seeders: $data['seeders'] ?? [],
            translations: $data['translations'] ?? [],
            assets: $data['assets'] ?? [],
            providers: $data['providers'] ?? [],
            filament: isset($data['filament']) ? PluginFilamentConfig::fromArray($data['filament']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'display_name' => $this->displayName,
            'description' => $this->description,
            'version' => $this->version,
            'minimum_nexus_version' => $this->minimumNexusVersion,
            'author' => $this->author,
            'license' => $this->license,
            'dependencies' => $this->dependencies,
            'permissions' => $this->permissions,
            'settings' => $this->settings,
            'menus' => $this->menus,
            'routes' => $this->routes,
            'migrations' => $this->migrations,
            'seeders' => $this->seeders,
            'translations' => $this->translations,
            'assets' => $this->assets,
            'providers' => $this->providers,
            'filament' => $this->filament?->toArray(),
        ];
    }
}
