<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\Enums\PluginLifecycleEvent;
use App\Domains\Plugin\Enums\PluginStatus;
use Tests\TestCase;

final class PluginStatusTest extends TestCase
{
    public function test_all_statuses_have_string_values(): void
    {
        $this->assertEquals('pending', PluginStatus::Pending->value);
        $this->assertEquals('installed', PluginStatus::Installed->value);
        $this->assertEquals('enabled', PluginStatus::Enabled->value);
        $this->assertEquals('disabled', PluginStatus::Disabled->value);
        $this->assertEquals('error', PluginStatus::Error->value);
        $this->assertEquals('upgrading', PluginStatus::Upgrading->value);
        $this->assertEquals('rolling_back', PluginStatus::RollingBack->value);
    }

    public function test_all_lifecycle_events_have_string_values(): void
    {
        $this->assertEquals('plugin.installing', PluginLifecycleEvent::Installing->value);
        $this->assertEquals('plugin.installed', PluginLifecycleEvent::Installed->value);
        $this->assertEquals('plugin.enabled', PluginLifecycleEvent::Enabled->value);
        $this->assertEquals('plugin.disabled', PluginLifecycleEvent::Disabled->value);
        $this->assertEquals('plugin.upgraded', PluginLifecycleEvent::Upgraded->value);
        $this->assertEquals('plugin.rolled_back', PluginLifecycleEvent::RolledBack->value);
        $this->assertEquals('plugin.uninstalled', PluginLifecycleEvent::Uninstalled->value);
    }

    public function test_can_create_from_value(): void
    {
        $this->assertEquals(PluginStatus::Enabled, PluginStatus::from('enabled'));
        $this->assertEquals(PluginLifecycleEvent::Installed, PluginLifecycleEvent::from('plugin.installed'));
    }
}
