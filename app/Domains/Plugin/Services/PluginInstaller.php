<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginDiscoveryInterface;
use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginLifecycleEvent;
use App\Domains\Plugin\Enums\PluginStatus;
use App\Domains\Plugin\Events\PluginInstalled;
use Exception;
use Illuminate\Filesystem\Filesystem;

final class PluginInstaller
{
    public function __construct(
        private PluginDiscoveryInterface $discovery,
        private PluginRegistryInterface $registry,
        private PluginMigrationLoader $migrationLoader,
        private PluginSeederLoader $seederLoader,
        private PluginPermissionRegistration $permissionRegistration,
        private PluginSettingsRegistration $settingsRegistration,
        private Filesystem $filesystem,
    ) {}

    public function install(string $path): PluginManifest
    {
        $manifest = $this->discovery->discoverFromPath($path);

        if ($manifest === null) {
            throw new Exception("Invalid plugin at path: {$path}. No valid plugin.json found.");
        }

        if ($this->registry->isRegistered($manifest->name)) {
            throw new Exception("Plugin '{$manifest->name}' is already installed.");
        }

        $this->validateDependencies($manifest);

        $pluginPath = config('plugin.install.base_path') . '/' . $manifest->name;
        if ($path !== $pluginPath && $this->filesystem->isDirectory($path)) {
            $this->filesystem->copyDirectory($path, $pluginPath);
        }

        $this->registry->register($manifest->name, $manifest);
        $this->migrationLoader->runPluginMigrations($manifest->name);
        $this->seederLoader->runPluginSeeders($manifest->name);
        $this->permissionRegistration->registerPermissions($manifest);
        $this->settingsRegistration->registerSettings($manifest);
        $this->registerPluginServiceProvider($manifest);

        event(new PluginInstalled($manifest->name, $manifest->version));

        return $manifest;
    }

    private function validateDependencies(PluginManifest $manifest): void
    {
        foreach ($manifest->dependencies as $dependency) {
            $depName = is_array($dependency) ? $dependency['name'] : $dependency;
            if (!$this->registry->isRegistered($depName)) {
                throw new Exception(
                    "Plugin '{$manifest->name}' requires '{$depName}' to be installed."
                );
            }
        }
    }

    private function registerPluginServiceProvider(PluginManifest $manifest): void
    {
        foreach ($manifest->providers as $provider) {
            if (class_exists($provider)) {
                app()->register($provider);
            }
        }
    }
}
