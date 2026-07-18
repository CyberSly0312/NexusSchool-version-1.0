<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class PluginRolledBack
{
    use Dispatchable;

    public function __construct(
        public readonly string $pluginName,
        public readonly string $fromVersion,
        public readonly string $toVersion,
    ) {}
}
