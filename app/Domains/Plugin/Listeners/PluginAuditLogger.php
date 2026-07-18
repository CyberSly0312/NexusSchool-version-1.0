<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Listeners;

use App\Domains\Plugin\Events\PluginDisabled;
use App\Domains\Plugin\Events\PluginEnabled;
use App\Domains\Plugin\Events\PluginInstalled;
use App\Domains\Plugin\Events\PluginRolledBack;
use App\Domains\Plugin\Events\PluginUninstalled;
use App\Domains\Plugin\Events\PluginUpgraded;
use Illuminate\Support\Facades\Log;

final class PluginAuditLogger
{
    public function handleInstalled(PluginInstalled $event): void
    {
        Log::info('Plugin installed', [
            'plugin' => $event->pluginName,
            'version' => $event->version,
        ]);
    }

    public function handleEnabled(PluginEnabled $event): void
    {
        Log::info('Plugin enabled', [
            'plugin' => $event->pluginName,
        ]);
    }

    public function handleDisabled(PluginDisabled $event): void
    {
        Log::info('Plugin disabled', [
            'plugin' => $event->pluginName,
        ]);
    }

    public function handleUpgraded(PluginUpgraded $event): void
    {
        Log::info('Plugin upgraded', [
            'plugin' => $event->pluginName,
            'from' => $event->fromVersion,
            'to' => $event->toVersion,
        ]);
    }

    public function handleUninstalled(PluginUninstalled $event): void
    {
        Log::info('Plugin uninstalled', [
            'plugin' => $event->pluginName,
            'version' => $event->version,
        ]);
    }

    public function handleRolledBack(PluginRolledBack $event): void
    {
        Log::info('Plugin rolled back', [
            'plugin' => $event->pluginName,
            'from' => $event->fromVersion,
            'to' => $event->toVersion,
        ]);
    }
}
