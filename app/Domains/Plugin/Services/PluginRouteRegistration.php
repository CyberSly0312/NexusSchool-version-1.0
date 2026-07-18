<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;

final class PluginRouteRegistration
{
    private array $registered = [];

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function registerRoutes(string $pluginName, string $pluginPath): void
    {
        $webRoutes = $pluginPath . '/Routes/web.php';
        $apiRoutes = $pluginPath . '/Routes/api.php';

        if ($this->filesystem->exists($webRoutes)) {
            Route::middleware(['web'])->group($webRoutes);
            $this->registered[$pluginName][] = $webRoutes;
        }

        if ($this->filesystem->exists($apiRoutes)) {
            Route::prefix('api/plugins/' . $pluginName)
                ->middleware(['api', 'auth:sanctum'])
                ->group($apiRoutes);
            $this->registered[$pluginName][] = $apiRoutes;
        }
    }

    public function unregisterRoutes(string $pluginName): void
    {
        unset($this->registered[$pluginName]);
    }

    public function getRegisteredRoutes(string $pluginName): array
    {
        return $this->registered[$pluginName] ?? [];
    }
}
