<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginDiscoveryInterface;
use App\Domains\Plugin\Contracts\PluginManagerInterface;
use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginStatus;
use Exception;
use Illuminate\Support\Collection;

final class PluginManager implements PluginManagerInterface
{
    public function __construct(
        private PluginDiscoveryInterface $discovery,
        private PluginRegistryInterface $registry,
        private PluginInstaller $installer,
        private PluginUninstaller $uninstaller,
        private PluginUpdater $updater,
    ) {}

    public function install(string $path): PluginManifest
    {
        return $this->installer->install($path);
    }

    public function uninstall(string $name): bool
    {
        return $this->uninstaller->uninstall($name);
    }

    public function enable(string $name): bool
    {
        $pluginData = $this->registry->get($name);

        if ($pluginData === null) {
            throw new Exception("Plugin '{$name}' is not installed.");
        }

        if ($pluginData['status'] === PluginStatus::Enabled->value) {
            return true;
        }

        $this->validateDependenciesEnabled($name);
        $this->registry->enable($name);
        $this->bootPlugin($name);

        return true;
    }

    public function disable(string $name): bool
    {
        $pluginData = $this->registry->get($name);

        if ($pluginData === null) {
            throw new Exception("Plugin '{$name}' is not installed.");
        }

        if ($pluginData['status'] !== PluginStatus::Enabled->value) {
            return true;
        }

        $this->checkDependentsNotEnabled($name);
        $this->shutdownPlugin($name);
        $this->registry->disable($name);

        return true;
    }

    public function upgrade(string $name): PluginManifest
    {
        return $this->updater->upgrade($name);
    }

    public function rollback(string $name, string $targetVersion): bool
    {
        return $this->updater->rollback($name, $targetVersion);
    }

    public function getInstalled(): Collection
    {
        return $this->registry->getAll();
    }

    public function getEnabledPlugins(): Collection
    {
        return $this->registry->getEnabled();
    }

    public function getPluginPath(string $name): string
    {
        return config('plugin.install.base_path') . '/' . $name;
    }

    public function discoverPlugins(): Collection
    {
        return $this->discovery->discoverAll();
    }

    private function validateDependenciesEnabled(string $name): void
    {
        $pluginData = $this->registry->get($name);
        $metadata = $pluginData['metadata'] ?? [];
        $dependencies = $metadata['dependencies'] ?? [];

        foreach ($dependencies as $dependency) {
            $depName = is_array($dependency) ? ($dependency['name'] ?? '') : $dependency;
            $required = is_array($dependency) ? ($dependency['required'] ?? true) : true;

            if ($required && !$this->registry->isEnabled($depName)) {
                throw new Exception(
                    "Plugin '{$name}' requires '{$depName}' to be enabled."
                );
            }
        }
    }

    private function checkDependentsNotEnabled(string $name): void
    {
        $allPlugins = $this->registry->getEnabled();

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
                        "Cannot disable '{$name}': plugin '{$plugin->name}' depends on it."
                    );
                }
            }
        }
    }

    private function bootPlugin(string $name): void
    {
        $pluginInstance = $this->resolvePluginInstance($name);
        if ($pluginInstance !== null) {
            $pluginInstance->boot();
        }
    }

    private function shutdownPlugin(string $name): void
    {
    }

    private function resolvePluginInstance(string $name): ?object
    {
        $pluginData = $this->registry->get($name);
        if ($pluginData === null) {
            return null;
        }

        $metadata = $pluginData['metadata'] ?? [];
        $providers = $metadata['providers'] ?? [];

        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $instance = app()->make($provider);
                if (method_exists($instance, 'boot')) {
                    return $instance;
                }
            }
        }

        return null;
    }
}
