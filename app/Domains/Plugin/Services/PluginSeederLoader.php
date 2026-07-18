<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

final class PluginSeederLoader
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function runPluginSeeders(string $pluginName): void
    {
        $path = config('plugin.install.base_path') . '/' . $pluginName . '/Database/Seeders';

        if (!$this->filesystem->isDirectory($path)) {
            return;
        }

        $files = $this->filesystem->glob($path . '/*.php');

        foreach ($files as $seederFile) {
            $seederClass = $this->resolveSeederClass($seederFile, $pluginName);
            if ($seederClass !== null && class_exists($seederClass)) {
                Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
            }
        }
    }

    private function resolveSeederClass(string $filePath, string $pluginName): ?string
    {
        $fileName = basename($filePath, '.php');
        return "NexusPlugins\\{$pluginName}\\Database\\Seeders\\{$fileName}";
    }
}
