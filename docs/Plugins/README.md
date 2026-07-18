# Nexus School ERP - Plugin Development Guide

## Overview

The Nexus School ERP Plugin Platform enables third-party developers to extend the system without modifying core code. Plugins are self-contained modules that integrate with the platform's DDD architecture, event system, multi-tenant infrastructure, and Filament admin panel.

## Architecture

### Plugin Location
Plugins are discovered from `app/Plugins/{PluginName}/` by default. The discovery engine can also scan Composer packages of type `nexus-plugin`.

### Plugin Structure
```
app/Plugins/{PluginName}/
├── plugin.json              # Required: Plugin manifest
├── Plugin.php               # Required: Main plugin class
├── PluginServiceProvider.php # Optional: Laravel service provider
├── Config/                  # Optional: Configuration files
├── Database/
│   ├── Migrations/          # Optional: Plugin database migrations
│   └── Seeders/             # Optional: Database seeders
├── Resources/
│   ├── Lang/                # Optional: Translation files
│   │   └── {locale}/
│   │       └── {group}.php
│   ├── Views/               # Optional: Blade views
│   └── Assets/              # Optional: JS/CSS assets
├── Routes/
│   ├── web.php              # Optional: Web routes
│   └── api.php              # Optional: API routes
├── Filament/                # Optional: Filament admin resources
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Events/                  # Optional: Plugin events
├── Listeners/               # Optional: Event listeners
└── Tests/                   # Optional: Plugin tests
    └── Unit/
```

## Plugin Manifest (plugin.json)

Every plugin must include a `plugin.json` manifest at its root:

```json
{
    "name": "nexus-grading",
    "display_name": "Advanced Grading",
    "description": "Advanced grading system with rubrics and weighted categories",
    "version": "1.0.0",
    "minimum_nexus_version": "1.0.0",
    "author": "Nexus Team",
    "license": "MIT",
    "dependencies": [
        { "name": "nexus-core", "version": "1.0.0", "required": true }
    ],
    "permissions": [
        "grading.view",
        "grading.create",
        "grading.manage"
    ],
    "settings": [
        {
            "key": "passing_score",
            "label": "Passing Score",
            "type": "number",
            "default": 60,
            "description": "Minimum score to pass",
            "required": true
        }
    ],
    "menus": [
        {
            "label": "Advanced Grading",
            "url": "/admin/advanced-grading",
            "icon": "heroicon-o-clipboard-document-check",
            "permission": "grading.view",
            "group": "Gradebook",
            "sort": 10
        }
    ],
    "routes": ["web", "api"],
    "migrations": ["create_rubrics_table"],
    "seeders": ["RubricSeeder"],
    "translations": ["en", "fr"],
    "assets": ["css/grading.css", "js/grading.js"],
    "providers": ["NexusPlugins\\Grading\\GradingServiceProvider"],
    "filament": {
        "resources": ["NexusPlugins\\Grading\\Filament\\Resources\\RubricResource"],
        "pages": [],
        "widgets": ["NexusPlugins\\Grading\\Filament\\Widgets\\GradingStatsWidget"],
        "navigation_group": "Gradebook",
        "navigation_group_icon": "heroicon-o-clipboard-document-check",
        "navigation_group_sort": 50
    }
}
```

## Plugin Class

Every plugin must have a main class implementing `PluginInterface`:

```php
<?php
declare(strict_types=1);

namespace NexusPlugins\Grading;

use App\Domains\Plugin\Contracts\PluginInterface;
use App\Domains\Plugin\DTOs\PluginManifest;

final class GradingPlugin implements PluginInterface
{
    private ?PluginManifest $manifest = null;

    public function boot(): void
    {
        // Register event listeners, publish assets, etc.
    }

    public function register(): void
    {
        // Bind services into the container
    }

    public function getManifest(): PluginManifest
    {
        return $this->manifest ??= PluginManifest::fromArray(
            json_decode(file_get_contents(__DIR__ . '/plugin.json'), true)
        );
    }

    public function getVersion(): string
    {
        return $this->getManifest()->version;
    }

    public function getName(): string
    {
        return $this->getManifest()->name;
    }

    public function isEnabled(): bool
    {
        return app(PluginRegistryInterface::class)->isEnabled($this->getName());
    }

    public function getServiceProvider(): ?\Illuminate\Support\ServiceProvider
    {
        return app(PluginServiceProvider::class);
    }
}
```

## Plugin Lifecycle

### Installation
```bash
php artisan plugin:install {path}
```
1. Reads and validates the plugin manifest
2. Validates all dependencies are met
3. Copies plugin to `app/Plugins/`
4. Registers the plugin in the database
5. Runs plugin migrations
6. Runs plugin seeders
7. Registers permissions with Spatie
8. Registers settings with defaults
9. Loads plugin service provider
10. Dispatches `PluginInstalled` event

### Enabling
```bash
php artisan plugin:enable {name}
```
1. Validates all dependencies are enabled
2. Updates status to `Enabled`
3. Boots the plugin
4. Registers event listeners
5. Loads plugin routes
6. Loads plugin translations
7. Publishes plugin assets
8. Registers Filament resources/pages/widgets

### Disabling
```bash
php artisan plugin:disable {name}
```
1. Checks no other enabled plugins depend on it
2. Shuts down the plugin
3. Updates status to `Disabled`
4. Unregisters event listeners
5. Unloads routes

### Upgrading
```bash
php artisan plugin:upgrade {name}
```
1. Validates new version is greater than current
2. Runs migration diff
3. Updates version in registry
4. Dispatches `PluginUpgraded` event

### Rollback
```bash
php artisan plugin:rollback {name} {version}
```
1. Validates target version is less than current
2. Updates version in registry
3. Dispatches `PluginRolledBack` event

### Uninstallation
```bash
php artisan plugin:uninstall {name}
```
1. Validates plugin is disabled
2. Checks no other plugins depend on it
3. Rolls back migrations
4. Removes permissions
5. Removes settings
6. Removes plugin files
7. Unregisters from database
8. Dispatches `PluginUninstalled` event

## Event System

Plugins can listen to all 143 core events and define their own events. Register listeners via the manifest:

```json
{
    "events": {
        "App\\Domains\\Student\\Events\\StudentCreated": [
            "NexusPlugins\\Grading\\Listeners\\StudentEnrollmentListener"
        ]
    }
}
```

## Multi-Tenancy

Plugin migrations run within the tenant database context. Plugin settings can be scoped per-school or global:

```php
// Global setting
PluginSetting::create([
    'plugin_id' => $pluginId,
    'school_id' => null,
    'key' => 'passing_score',
    'value' => 60,
]);

// Per-school override
PluginSetting::create([
    'plugin_id' => $pluginId,
    'school_id' => $schoolId,
    'key' => 'passing_score',
    'value' => 70,
]);
```

## Filament Integration

Plugins can register Filament Resources, Pages, and Widgets:

```php
// In plugin.json
"filament": {
    "resources": ["NexusPlugins\\Grading\\Filament\\Resources\\RubricResource"],
    "widgets": ["NexusPlugins\\Grading\\Filament\\Widgets\\GradingStatsWidget"],
    "navigation_group": "Gradebook",
    "navigation_group_icon": "heroicon-o-clipboard-document-check"
}
```

## Security

Every plugin must:
- **Declare permissions** - All access control is explicit
- **Declare migrations** - No untracked schema changes
- **Declare routes** - All routes are namespaced under `/api/plugins/{name}`
- **Declare assets** - Published to `public/plugins/{name}/`
- **Declare dependencies** - Enforced before enable
- **Declare compatible versions** - `minimum_nexus_version` checked

## API Reference

### PluginManager
| Method | Description |
|--------|-------------|
| `install(string $path): PluginManifest` | Install a plugin from path |
| `uninstall(string $name): bool` | Remove a plugin |
| `enable(string $name): bool` | Enable a plugin |
| `disable(string $name): bool` | Disable a plugin |
| `upgrade(string $name): PluginManifest` | Upgrade to latest version |
| `rollback(string $name, string $version): bool` | Rollback to version |
| `getInstalled(): Collection` | List all installed plugins |
| `getEnabledPlugins(): Collection` | List enabled plugins |

### PluginRegistry
| Method | Description |
|--------|-------------|
| `register(string $name, PluginManifest $manifest): void` | Register plugin in database |
| `unregister(string $name): void` | Remove from database |
| `enable(string $name): void` | Set status to enabled |
| `disable(string $name): void` | Set status to disabled |
| `get(string $name): ?array` | Get plugin data |
| `getAll(): Collection` | Get all plugins |
| `isRegistered(string $name): bool` | Check if registered |
| `isEnabled(string $name): bool` | Check if enabled |

### PluginConfigurationService
| Method | Description |
|--------|-------------|
| `get(string $plugin, string $key, ?string $schoolId): mixed` | Get setting value |
| `set(string $plugin, string $key, mixed $value, ?string $schoolId): void` | Set value |
| `getMany(string $plugin, ?string $schoolId): array` | Get all settings |
| `reset(string $plugin, ?string $schoolId): void` | Reset settings |