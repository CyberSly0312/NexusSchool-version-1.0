<?php
declare(strict_types=1);

namespace App\Domains\Plugin;

use App\Domains\Plugin\Contracts\PluginDiscoveryInterface;
use App\Domains\Plugin\Contracts\PluginManagerInterface;
use App\Domains\Plugin\Contracts\PluginRegistryInterface;
use App\Domains\Plugin\Services\FilamentPluginIntegration;
use App\Domains\Plugin\Services\PluginAssetLoader;
use App\Domains\Plugin\Services\PluginConfigurationService;
use App\Domains\Plugin\Services\PluginDiscoveryEngine;
use App\Domains\Plugin\Services\PluginEventRegistration;
use App\Domains\Plugin\Services\PluginManager;
use App\Domains\Plugin\Services\PluginMigrationLoader;
use App\Domains\Plugin\Services\PluginPermissionRegistration;
use App\Domains\Plugin\Services\PluginRegistry;
use App\Domains\Plugin\Services\PluginRouteRegistration;
use App\Domains\Plugin\Services\PluginSeederLoader;
use App\Domains\Plugin\Services\PluginSettingsRegistration;
use App\Domains\Plugin\Services\PluginTranslationLoader;
use Illuminate\Support\ServiceProvider;

final class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginDiscoveryInterface::class, PluginDiscoveryEngine::class);
        $this->app->singleton(PluginRegistryInterface::class, PluginRegistry::class);
        $this->app->singleton(PluginManagerInterface::class, PluginManager::class);

        $this->app->singleton(PluginMigrationLoader::class);
        $this->app->singleton(PluginSeederLoader::class);
        $this->app->singleton(PluginEventRegistration::class);
        $this->app->singleton(PluginRouteRegistration::class);
        $this->app->singleton(PluginTranslationLoader::class);
        $this->app->singleton(PluginAssetLoader::class);
        $this->app->singleton(PluginPermissionRegistration::class);
        $this->app->singleton(PluginMenuRegistration::class);
        $this->app->singleton(PluginSettingsRegistration::class);
        $this->app->singleton(PluginConfigurationService::class);
        $this->app->singleton(FilamentPluginIntegration::class);
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/plugin.php',
            'plugin'
        );

        $this->registerEvents();
    }

    private function registerEvents(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginInstalled::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleInstalled']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginEnabled::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleEnabled']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginDisabled::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleDisabled']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginUpgraded::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleUpgraded']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginUninstalled::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleUninstalled']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Domains\Plugin\Events\PluginRolledBack::class,
            [\App\Domains\Plugin\Listeners\PluginAuditLogger::class, 'handleRolledBack']
        );
    }
}
