<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Contracts;

use App\Domains\Plugin\DTOs\PluginManifest;
use Illuminate\Support\ServiceProvider;

interface PluginInterface
{
    public function boot(): void;

    public function register(): void;

    public function getManifest(): PluginManifest;

    public function getVersion(): string;

    public function getName(): string;

    public function isEnabled(): bool;

    public function getServiceProvider(): ?ServiceProvider;
}
