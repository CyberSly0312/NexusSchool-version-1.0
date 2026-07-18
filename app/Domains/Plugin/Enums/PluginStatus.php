<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Enums;

enum PluginStatus: string
{
    case Pending = 'pending';
    case Installed = 'installed';
    case Enabled = 'enabled';
    case Disabled = 'disabled';
    case Error = 'error';
    case Upgrading = 'upgrading';
    case RollingBack = 'rolling_back';
}
