<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginStatus;
use App\Models\Plugin;
use Illuminate\Support\Collection;

final class PluginRegistry implements PluginRegistryInterface
{
    public function register(string $name, PluginManifest $manifest): void
    {
        Plugin::create([
            'name' => $name,
            'display_name' => $manifest->displayName,
            'description' => $manifest->description,
            'version' => $manifest->version,
            'author' => $manifest->author,
            'license' => $manifest->license,
            'status' => PluginStatus::Installed,
            'path' => app_path("Plugins/{$name}"),
            'settings' => collect($manifest->settings)->mapWithKeys(
                fn(array $s) => [$s['key'] => $s['default'] ?? null]
            )->toArray(),
            'metadata' => [
                'author' => $manifest->author,
                'license' => $manifest->license,
                'minimum_nexus_version' => $manifest->minimumNexusVersion,
                'dependencies' => $manifest->dependencies,
                'permissions' => $manifest->permissions,
            ],
        ]);
    }

    public function unregister(string $name): void
    {
        $plugin = $this->getByName($name);
        if ($plugin) {
            $plugin->delete();
        }
    }

    public function enable(string $name): void
    {
        $plugin = $this->getByName($name);
        if ($plugin) {
            $plugin->update(['status' => PluginStatus::Enabled]);
        }
    }

    public function disable(string $name): void
    {
        $plugin = $this->getByName($name);
        if ($plugin) {
            $plugin->update(['status' => PluginStatus::Disabled]);
        }
    }

    public function get(string $name): ?array
    {
        $plugin = $this->getByName($name);

        return $plugin?->toArray();
    }

    public function getAll(): Collection
    {
        return Plugin::all();
    }

    public function getEnabled(): Collection
    {
        return Plugin::enabled()->get();
    }

    public function isRegistered(string $name): bool
    {
        return $this->getByName($name) !== null;
    }

    public function isEnabled(string $name): bool
    {
        $plugin = $this->getByName($name);

        return $plugin?->status === PluginStatus::Enabled;
    }

    public function updateVersion(string $name, string $version): void
    {
        $plugin = $this->getByName($name);
        if ($plugin) {
            $plugin->update(['version' => $version]);
        }
    }

    private function getByName(string $name): ?Plugin
    {
        return Plugin::where('name', $name)->first();
    }
}
