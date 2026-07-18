<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Contracts;

use App\Domains\Plugin\DTOs\PluginManifest;
use Illuminate\Support\Collection;

interface PluginRegistryInterface
{
    public function register(string $name, PluginManifest $manifest): void;

    public function unregister(string $name): void;

    public function enable(string $name): void;

    public function disable(string $name): void;

    public function get(string $name): ?array;

    public function getAll(): Collection;

    public function getEnabled(): Collection;

    public function isRegistered(string $name): bool;

    public function isEnabled(string $name): bool;

    public function updateVersion(string $name, string $version): void;
}
