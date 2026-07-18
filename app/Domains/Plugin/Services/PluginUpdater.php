<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginDiscoveryInterface;
use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginStatus;
use App\Domains\Plugin\Events\PluginUpgraded;
use Exception;
use Illuminate\Filesystem\Filesystem;

final class PluginUpdater
{
    public function __construct(
        private PluginDiscoveryInterface $discovery,
        private PluginRegistryInterface $registry,
        private PluginMigrationLoader $migrationLoader,
        private Filesystem $filesystem,
    ) {}

    public function upgrade(string $name): PluginManifest
    {
        $pluginData = $this->registry->get($name);

        if ($pluginData === null) {
            throw new Exception("Plugin '{$name}' is not installed.");
        }

        $pluginPath = $pluginData['path'] ?? config('plugin.install.base_path') . '/' . $name;
        $newManifest = $this->discovery->discoverFromPath($pluginPath);

        if ($newManifest === null) {
            throw new Exception("Cannot read plugin manifest at: {$pluginPath}");
        }

        $this->validateDependencies($newManifest);

        $currentVersion = $pluginData['version'];

        if (version_compare($newManifest->version, $currentVersion, '<=')) {
            throw new Exception(
                "Plugin '{$name}' is already at version {$currentVersion}. " .
                "New version must be greater."
            );
        }

        $this->registry->updateVersion($name, $newManifest->version);

        $this->migrationLoader->runPluginMigrations($name);

        event(new PluginUpgraded($name, $currentVersion, $newManifest->version));

        return $newManifest;
    }

    public function rollback(string $name, string $targetVersion): bool
    {
        $pluginData = $this->registry->get($name);

        if ($pluginData === null) {
            throw new Exception("Plugin '{$name}' is not installed.");
        }

        if (version_compare($targetVersion, $pluginData['version'], '>=')) {
            throw new Exception(
                "Target version '{$targetVersion}' must be less than current version '{$pluginData['version']}'."
            );
        }

        $this->registry->updateVersion($name, $targetVersion);

        event(new \App\Domains\Plugin\Events\PluginRolledBack($name, $pluginData['version'], $targetVersion));

        return true;
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
}
