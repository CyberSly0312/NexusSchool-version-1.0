<?php
declare(strict_types=1);

namespace Tests\Feature\Plugin;

use App\Domains\Plugin\Enums\PluginStatus;
use App\Domains\Plugin\Services\PluginManager;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

final class PluginLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private PluginManager $manager;
    private Filesystem $filesystem;
    private string $pluginPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = app(PluginManager::class);
        $this->filesystem = new Filesystem();
        $this->pluginPath = sys_get_temp_dir() . '/nexus_plugin_test_' . uniqid();

        $this->filesystem->makeDirectory($this->pluginPath, 0755, true);
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->isDirectory($this->pluginPath)) {
            $this->filesystem->deleteDirectory($this->pluginPath);
        }
        parent::tearDown();
    }

    private function createTestPlugin(string $name = 'test-plugin', string $version = '1.0.0'): string
    {
        $pluginDir = $this->pluginPath . '/' . $name;
        $this->filesystem->makeDirectory($pluginDir, 0755, true);
        $this->filesystem->put($pluginDir . '/plugin.json', json_encode([
            'name' => $name,
            'display_name' => 'Test Plugin',
            'description' => 'A test plugin for lifecycle testing',
            'version' => $version,
            'minimum_nexus_version' => '1.0.0',
            'author' => 'Test Author',
            'license' => 'MIT',
            'permissions' => ['test-plugin.view', 'test-plugin.manage'],
            'settings' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'text', 'default' => ''],
            ],
        ]));

        return $pluginDir;
    }

    public function test_full_plugin_lifecycle(): void
    {
        $pluginDir = $this->createTestPlugin();

        $manifest = $this->manager->install($pluginDir);
        $this->assertEquals('test-plugin', $manifest->name);

        $plugin = Plugin::where('name', 'test-plugin')->first();
        $this->assertNotNull($plugin);
        $this->assertEquals(PluginStatus::Installed, $plugin->status);

        $this->manager->enable('test-plugin');
        $plugin->refresh();
        $this->assertEquals(PluginStatus::Enabled, $plugin->status);

        $this->manager->disable('test-plugin');
        $plugin->refresh();
        $this->assertEquals(PluginStatus::Disabled, $plugin->status);
    }

    public function test_cannot_install_duplicate_plugin(): void
    {
        $pluginDir = $this->createTestPlugin();
        $this->manager->install($pluginDir);

        $this->expectException(\Exception::class);
        $this->manager->install($pluginDir);
    }

    public function test_cannot_enable_nonexistent_plugin(): void
    {
        $this->expectException(\Exception::class);
        $this->manager->enable('nonexistent');
    }

    public function test_cannot_uninstall_enabled_plugin(): void
    {
        $pluginDir = $this->createTestPlugin();
        $this->manager->install($pluginDir);
        $this->manager->enable('test-plugin');

        $this->expectException(\Exception::class);
        $this->manager->uninstall('test-plugin');
    }

    public function test_can_uninstall_disabled_plugin(): void
    {
        $pluginDir = $this->createTestPlugin();
        $this->manager->install($pluginDir);

        $result = $this->manager->uninstall('test-plugin');
        $this->assertTrue($result);

        $plugin = Plugin::where('name', 'test-plugin')->first();
        $this->assertNull($plugin);
    }

    public function test_can_list_installed_plugins(): void
    {
        $this->createTestPlugin('plugin-a');
        $this->createTestPlugin('plugin-b');

        $this->manager->install($this->pluginPath . '/plugin-a');
        $this->manager->install($this->pluginPath . '/plugin-b');

        $installed = $this->manager->getInstalled();
        $this->assertCount(2, $installed);
    }

    public function test_can_list_enabled_plugins(): void
    {
        $this->createTestPlugin('plugin-a');
        $this->createTestPlugin('plugin-b');

        $this->manager->install($this->pluginPath . '/plugin-a');
        $this->manager->install($this->pluginPath . '/plugin-b');
        $this->manager->enable('plugin-a');

        $enabled = $this->manager->getEnabledPlugins();
        $this->assertCount(1, $enabled);
        $this->assertEquals('plugin-a', $enabled->first()->name);
    }

    public function test_plugin_installed_event_dispatched(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Domains\Plugin\Events\PluginInstalled::class,
        ]);

        $pluginDir = $this->createTestPlugin();
        $this->manager->install($pluginDir);

        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Domains\Plugin\Events\PluginInstalled::class,
            fn($event) => $event->pluginName === 'test-plugin' && $event->version === '1.0.0'
        );
    }

    public function test_dependency_check阻止_enable(): void
    {
        $this->createTestPlugin('plugin-a');
        $this->createTestPlugin('plugin-b', '1.0.0');

        $pluginBDir = $this->pluginPath . '/plugin-b';
        $this->filesystem->put($pluginBDir . '/plugin.json', json_encode([
            'name' => 'plugin-b',
            'display_name' => 'Plugin B',
            'version' => '1.0.0',
            'minimum_nexus_version' => '1.0.0',
            'dependencies' => [['name' => 'plugin-a', 'version' => '1.0.0', 'required' => true]],
        ]));

        $this->manager->install($this->pluginPath . '/plugin-a');
        $this->manager->install($pluginBDir);

        $this->expectException(\Exception::class);
        $this->manager->enable('plugin-b');
    }
}
