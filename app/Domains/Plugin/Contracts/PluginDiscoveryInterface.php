<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Contracts;

use App\Domains\Plugin\DTOs\PluginManifest;
use Illuminate\Support\Collection;

interface PluginDiscoveryInterface
{
    public function discoverAll(): Collection;

    public function discoverFromPath(string $path): ?PluginManifest;

    public function getPluginPaths(): array;

    public function isPlugin(string $path): bool;
}
