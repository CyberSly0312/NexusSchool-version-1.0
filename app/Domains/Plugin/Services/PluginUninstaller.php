<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginStatus;
use App\Domains\Plugin\Events\PluginUninstalled;
use App\Domains\Plugin\Services\PluginMigrationLoader;
use App\Domains\Plugin\Services\PluginPermissionRegistration;
use App\Domains\Plugin\Services\PluginSettingsRegistration;
use Exception;
use Illuminate\Filesystem\Filesystem;

final class PluginUninstaller
{
    public function __construct(
        private PluginRegistryInterface $registry,
        private PluginMigrationLoader $migrationLoader,
        private PluginPermissionRegistration $permissionRegistration,
        private PluginSettingsRegistration $settingsRegistration,
        private Filesystem $filesystem,
    ) {}

    public function uninstall(string $name): bool
    {
        $pluginData = $this->registry->get($name);

        if ($pluginData === null) {
            throw new Exception("Plugin '{$name}' is not installed.");
        }

        if ($pluginData['status'] === PluginStatus::Enabled->value) {
            throw new Exception("Cannot uninstall an enabled plugin. Disable it first.");
        }

        $this->checkDependents($name);

        $this->settingsRegistration->unregisterSettings($name);
        $this->permissionRegistration->unregisterPermissions($name);
        $this->migrationLoader->rollbackPluginMigrations($name);
        $this->registry->unregister($name);

        $pluginPath = config('plugin.install.base_path') . '/' . $name;
        if ($this->filesystem->isDirectory($pluginPath)) {
            $this->filesystem->deleteDirectory($pluginPath);
        }

        event(new PluginUninstalled($name, $pluginData['version']));

        return true;
    }

    private function checkDependents(string $name): void
    {
        $allPlugins = $this->registry->getAll();

        foreach ($allPlugins as $plugin) {
            if ($plugin->name === $name) {
                continue;
            }

            $metadata = $plugin->metadata ?? [];
            $dependencies = $metadata['dependencies'] ?? [];

            foreach ($dependencies as $dep) {
                $depName = is_array($dep) ? ($dep['name'] ?? '') : $dep;
                if ($depName === $name) {
                    throw new Exception(
                        "Cannot uninstall '{$name}': plugin '{$plugin->name}' depends on it."
                    );
                }
            }
        }
    }
}
