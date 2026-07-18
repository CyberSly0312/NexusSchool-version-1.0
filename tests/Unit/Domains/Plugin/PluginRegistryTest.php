<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Enums\PluginStatus;
use App\Domains\Plugin\Services\PluginRegistry;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PluginRegistryTest extends TestCase
{
    use RefreshDatabase;

    private PluginRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new PluginRegistry();
    }

    private function makeManifest(array $overrides = []): PluginManifest
    {
        return PluginManifest::fromArray(array_merge([
            'name' => 'test-plugin',
            'display_name' => 'Test Plugin',
            'description' => 'A test plugin',
            'version' => '1.0.0',
            'minimum_nexus_version' => '1.0.0',
            'author' => 'Test Author',
            'license' => 'MIT',
        ], $overrides));
    }

    public function test_can_register_plugin(): void
    {
        $manifest = $this->makeManifest();
        $this->registry->register('test-plugin', $manifest);

        $plugin = Plugin::where('name', 'test-plugin')->first();
        $this->assertNotNull($plugin);
        $this->assertEquals('Test Plugin', $plugin->display_name);
        $this->assertEquals(PluginStatus::Installed, $plugin->status);
    }

    public function test_can_unregister_plugin(): void
    {
        $manifest = $this->makeManifest();
        $this->registry->register('test-plugin', $manifest);
        $this->assertTrue($this->registry->isRegistered('test-plugin'));

        $this->registry->unregister('test-plugin');
        $this->assertFalse($this->registry->isRegistered('test-plugin'));
    }

    public function test_can_enable_plugin(): void
    {
        $manifest = $this->makeManifest();
        $this->registry->register('test-plugin', $manifest);
        $this->registry->enable('test-plugin');

        $this->assertTrue($this->registry->isEnabled('test-plugin'));
    }

    public function test_can_disable_plugin(): void
    {
        $manifest = $this->makeManifest();
        $this->registry->register('test-plugin', $manifest);
        $this->registry->enable('test-plugin');
        $this->registry->disable('test-plugin');

        $this->assertFalse($this->registry->isEnabled('test-plugin'));
    }

    public function test_can_get_all_plugins(): void
    {
        $manifest1 = $this->makeManifest(['name' => 'plugin-a', 'display_name' => 'Plugin A']);
        $manifest2 = $this->makeManifest(['name' => 'plugin-b', 'display_name' => 'Plugin B']);

        $this->registry->register('plugin-a', $manifest1);
        $this->registry->register('plugin-b', $manifest2);

        $all = $this->registry->getAll();
        $this->assertCount(2, $all);
    }

    public function test_can_get_enabled_plugins(): void
    {
        $manifest1 = $this->makeManifest(['name' => 'plugin-a']);
        $manifest2 = $this->makeManifest(['name' => 'plugin-b']);

        $this->registry->register('plugin-a', $manifest1);
        $this->registry->register('plugin-b', $manifest2);
        $this->registry->enable('plugin-a');

        $enabled = $this->registry->getEnabled();
        $this->assertCount(1, $enabled);
        $this->assertEquals('plugin-a', $enabled->first()->name);
    }

    public function test_can_update_version(): void
    {
        $manifest = $this->makeManifest(['version' => '1.0.0']);
        $this->registry->register('test-plugin', $manifest);

        $this->registry->updateVersion('test-plugin', '2.0.0');

        $plugin = $this->registry->get('test-plugin');
        $this->assertEquals('2.0.0', $plugin['version']);
    }

    public function test_is_registered_returns_false_for_unknown(): void
    {
        $this->assertFalse($this->registry->isRegistered('nonexistent'));
    }

    public function test_get_returns_null_for_unknown(): void
    {
        $this->assertNull($this->registry->get('nonexistent'));
    }
}
