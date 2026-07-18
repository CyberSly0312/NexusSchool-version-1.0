<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\Services\PluginDiscoveryEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

final class PluginDiscoveryEngineTest extends TestCase
{
    use RefreshDatabase;

    private PluginDiscoveryEngine $discovery;
    private Filesystem $filesystem;
    private string $testPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->discovery = new PluginDiscoveryEngine($this->filesystem);
        $this->testPath = sys_get_temp_dir() . '/nexus_test_plugins_' . uniqid();
        $this->filesystem->makeDirectory($this->testPath, 0755, true);
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->isDirectory($this->testPath)) {
            $this->filesystem->deleteDirectory($this->testPath);
        }
        parent::tearDown();
    }

    public function test_can_discover_plugin_from_valid_path(): void
    {
        $pluginDir = $this->testPath . '/test-plugin';
        $this->filesystem->makeDirectory($pluginDir, 0755, true);
        $this->filesystem->put($pluginDir . '/plugin.json', json_encode([
            'name' => 'test-plugin',
            'display_name' => 'Test Plugin',
            'description' => 'A test plugin',
            'version' => '1.0.0',
            'minimum_nexus_version' => '1.0.0',
            'author' => 'Test',
            'license' => 'MIT',
        ]));

        $manifest = $this->discovery->discoverFromPath($pluginDir);
        $this->assertNotNull($manifest);
        $this->assertEquals('test-plugin', $manifest->name);
        $this->assertEquals('1.0.0', $manifest->version);
    }

    public function test_returns_null_for_invalid_path(): void
    {
        $manifest = $this->discovery->discoverFromPath('/nonexistent/path');
        $this->assertNull($manifest);
    }

    public function test_returns_null_for_missing_plugin_json(): void
    {
        $pluginDir = $this->testPath . '/empty-plugin';
        $this->filesystem->makeDirectory($pluginDir, 0755, true);

        $manifest = $this->discovery->discoverFromPath($pluginDir);
        $this->assertNull($manifest);
    }

    public function test_is_plugin_returns_true_for_valid_plugin(): void
    {
        $pluginDir = $this->testPath . '/valid-plugin';
        $this->filesystem->makeDirectory($pluginDir, 0755, true);
        $this->filesystem->put($pluginDir . '/plugin.json', '{}');

        $this->assertTrue($this->discovery->isPlugin($pluginDir));
    }

    public function test_is_plugin_returns_false_for_non_plugin(): void
    {
        $pluginDir = $this->testPath . '/not-a-plugin';
        $this->filesystem->makeDirectory($pluginDir, 0755, true);

        $this->assertFalse($this->discovery->isPlugin($pluginDir));
    }
}
