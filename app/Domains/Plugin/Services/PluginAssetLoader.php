<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Filesystem\Filesystem;

final class PluginAssetLoader
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function publishAssets(string $pluginName, string $pluginPath): void
    {
        $assetsPath = $pluginPath . '/Resources/Assets';

        if (!$this->filesystem->isDirectory($assetsPath)) {
            return;
        }

        $publicPath = config('plugin.assets.public_path') . '/' . $pluginName;

        if (!$this->filesystem->isDirectory($publicPath)) {
            $this->filesystem->makeDirectory($publicPath, 0755, true);
        }

        $this->filesystem->copyDirectory($assetsPath, $publicPath);
    }

    public function removeAssets(string $pluginName): void
    {
        $publicPath = config('plugin.assets.public_path') . '/' . $pluginName;

        if ($this->filesystem->isDirectory($publicPath)) {
            $this->filesystem->deleteDirectory($publicPath);
        }
    }

    public function getAssetUrl(string $pluginName, string $assetPath): string
    {
        return asset("plugins/{$pluginName}/{$assetPath}");
    }
}
