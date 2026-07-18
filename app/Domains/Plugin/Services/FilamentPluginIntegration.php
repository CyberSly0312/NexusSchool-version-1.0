<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\DTOs\PluginManifest;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FilamentPluginIntegration
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function registerPluginResources(Panel $panel, PluginManifest $manifest): void
    {
        $filamentConfig = $manifest->filament;
        if ($filamentConfig === null) {
            return;
        }

        $pluginPath = config('plugin.install.base_path') . '/' . $manifest->name;

        foreach ($filamentConfig->resources as $resource) {
            $this->registerResource($panel, $resource, $manifest->name, $pluginPath);
        }

        foreach ($filamentConfig->pages as $page) {
            $this->registerPage($panel, $page, $manifest->name, $pluginPath);
        }

        foreach ($filamentConfig->widgets as $widget) {
            $this->registerWidget($panel, $widget, $manifest->name, $pluginPath);
        }
    }

    public function unregisterPluginResources(Panel $panel, string $pluginName): void
    {
    }

    public function discoverPluginFilamentClasses(string $pluginName, string $pluginPath): array
    {
        $classes = [
            'resources' => [],
            'pages' => [],
            'widgets' => [],
        ];

        $filamentPath = $pluginPath . '/Filament';

        if (!$this->filesystem->isDirectory($filamentPath)) {
            return $classes;
        }

        $resourcePath = $filamentPath . '/Resources';
        if ($this->filesystem->isDirectory($resourcePath)) {
            $classes['resources'] = $this->discoverClassesInPath(
                $resourcePath,
                "NexusPlugins\\{$pluginName}\\Filament\\Resources"
            );
        }

        $pagesPath = $filamentPath . '/Pages';
        if ($this->filesystem->isDirectory($pagesPath)) {
            $classes['pages'] = $this->discoverClassesInPath(
                $pagesPath,
                "NexusPlugins\\{$pluginName}\\Filament\\Pages"
            );
        }

        $widgetsPath = $filamentPath . '/Widgets';
        if ($this->filesystem->isDirectory($widgetsPath)) {
            $classes['widgets'] = $this->discoverClassesInPath(
                $widgetsPath,
                "NexusPlugins\\{$pluginName}\\Filament\\Widgets"
            );
        }

        return $classes;
    }

    private function registerResource(Panel $panel, string $resourceClass, string $pluginName, string $pluginPath): void
    {
        $resolvedClass = $this->resolveClass($resourceClass, $pluginName, $pluginPath);
        if ($resolvedClass !== null && class_exists($resolvedClass)) {
            $panel->resources([...$panel->getResource(), $resolvedClass]);
        }
    }

    private function registerPage(Panel $panel, string $pageClass, string $pluginName, string $pluginPath): void
    {
        $resolvedClass = $this->resolveClass($pageClass, $pluginName, $pluginPath);
        if ($resolvedClass !== null && class_exists($resolvedClass)) {
            $panel->pages([...$panel->getPages(), $resolvedClass]);
        }
    }

    private function registerWidget(Panel $panel, string $widgetClass, string $pluginName, string $pluginPath): void
    {
        $resolvedClass = $this->resolveClass($widgetClass, $pluginName, $pluginPath);
        if ($resolvedClass !== null && class_exists($resolvedClass)) {
            $panel->widgets([...$panel->getWidgets(), $resolvedClass]);
        }
    }

    private function resolveClass(string $classOrPath, string $pluginName, string $pluginPath): ?string
    {
        if (class_exists($classOrPath)) {
            return $classOrPath;
        }

        $namespace = "NexusPlugins\\{$pluginName}\\Filament";
        $relativePath = Str::after($classOrPath, $pluginPath);
        $relativePath = ltrim($relativePath, '/\\');
        $className = basename($relativePath, '.php');
        $namespacePath = dirname($relativePath);
        $namespaceSuffix = str_replace(['/', '\\'], '\\', $namespacePath);

        return $namespace . ($namespaceSuffix ? "\\{$namespaceSuffix}" : '') . "\\{$className}";
    }

    private function discoverClassesInPath(string $path, string $namespace): array
    {
        $classes = [];
        $files = $this->filesystem->glob($path . '/*.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $classes[] = $namespace . '\\' . $className;
        }

        return $classes;
    }
}
