<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Contracts;

use App\Domains\Plugin\DTOs\PluginManifest;

interface PluginManagerInterface
{
    public function install(string $path): PluginManifest;

    public function uninstall(string $name): bool;

    public function enable(string $name): bool;

    public function disable(string $name): bool;

    public function upgrade(string $name): PluginManifest;

    public function rollback(string $name, string $targetVersion): bool;

    public function getInstalled(): \Illuminate\Support\Collection;

    public function getEnabledPlugins(): \Illuminate\Support\Collection;

    public function getPluginPath(string $name): string;
}
