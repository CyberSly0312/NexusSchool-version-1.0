<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\Contracts\PluginDiscoveryInterface;
use App\Domains\Plugin\DTOs\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

final class PluginDiscoveryEngine implements PluginDiscoveryInterface
{
    private array $pluginPaths;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->pluginPaths = config('plugin.discovery.paths', [app_path('Plugins')]);
    }

    public function discoverAll(): Collection
    {
        $plugins = collect();

        foreach ($this->pluginPaths as $basePath) {
            if (!$this->filesystem->isDirectory($basePath)) {
                continue;
            }

            $directories = $this->filesystem->directories($basePath);

            foreach ($directories as $directory) {
                $manifest = $this->discoverFromPath($directory);
                if ($manifest !== null) {
                    $plugins->put($manifest->name, $manifest);
                }
            }
        }

        return $plugins;
    }

    public function discoverFromPath(string $path): ?PluginManifest
    {
        $manifestPath = $path . '/plugin.json';

        if (!$this->filesystem->exists($manifestPath)) {
            return null;
        }

        $contents = $this->filesystem->get($manifestPath);
        $data = json_decode($contents, true);

        if (!is_array($data) || !isset($data['name'])) {
            return null;
        }

        return PluginManifest::fromArray($data);
    }

    public function getPluginPaths(): array
    {
        $paths = [];

        foreach ($this->pluginPaths as $basePath) {
            if (!$this->filesystem->isDirectory($basePath)) {
                continue;
            }

            foreach ($this->filesystem->directories($basePath) as $directory) {
                if ($this->isPlugin($directory)) {
                    $paths[] = $directory;
                }
            }
        }

        return $paths;
    }

    public function isPlugin(string $path): bool
    {
        return $this->filesystem->exists($path . '/plugin.json');
    }
}
