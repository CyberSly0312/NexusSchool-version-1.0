<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Enums;

enum PluginLifecycleEvent: string
{
    case Installing = 'plugin.installing';
    case Installed = 'plugin.installed';
    case Enabling = 'plugin.enabling';
    case Enabled = 'plugin.enabled';
    case Disabling = 'plugin.disabling';
    case Disabled = 'plugin.disabled';
    case Upgrading = 'plugin.upgrading';
    case Upgraded = 'plugin.upgraded';
    case RollingBack = 'plugin.rolling_back';
    case RolledBack = 'plugin.rolled_back';
    case Uninstalling = 'plugin.uninstalling';
    case Uninstalled = 'plugin.uninstalled';
}
