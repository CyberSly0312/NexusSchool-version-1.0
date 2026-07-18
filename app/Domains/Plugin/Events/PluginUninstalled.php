<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class PluginUninstalled
{
    use Dispatchable;

    public function __construct(
        public readonly string $pluginName,
        public readonly string $version,
    ) {}
}
