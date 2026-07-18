<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\DTOs\PluginFilamentConfig;
use App\Domains\Plugin\DTOs\PluginManifest;
use Tests\TestCase;

final class PluginManifestTest extends TestCase
{
    public function test_can_create_manifest_from_array(): void
    {
        $data = [
            'name' => 'test-plugin',
            'display_name' => 'Test Plugin',
            'description' => 'A test',
            'version' => '2.0.0',
            'minimum_nexus_version' => '1.0.0',
            'author' => 'Author',
            'license' => 'MIT',
            'dependencies' => [['name' => 'dep-a', 'version' => '1.0', 'required' => true]],
            'permissions' => ['test.view', 'test.manage'],
            'settings' => [['key' => 'api_key', 'label' => 'API Key', 'type' => 'text']],
        ];

        $manifest = PluginManifest::fromArray($data);

        $this->assertEquals('test-plugin', $manifest->name);
        $this->assertEquals('Test Plugin', $manifest->displayName);
        $this->assertEquals('2.0.0', $manifest->version);
        $this->assertCount(1, $manifest->dependencies);
        $this->assertCount(2, $manifest->permissions);
        $this->assertCount(1, $manifest->settings);
    }

    public function test_can_convert_manifest_to_array(): void
    {
        $manifest = PluginManifest::fromArray([
            'name' => 'my-plugin',
            'display_name' => 'My Plugin',
            'version' => '1.0.0',
        ]);

        $array = $manifest->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('my-plugin', $array['name']);
        $this->assertEquals('My Plugin', $array['display_name']);
        $this->assertEquals('1.0.0', $array['version']);
    }

    public function test_manifest_defaults_applied(): void
    {
        $manifest = PluginManifest::fromArray([
            'name' => 'minimal',
            'display_name' => 'Minimal',
            'version' => '1.0.0',
        ]);

        $this->assertEquals('MIT', $manifest->license);
        $this->assertEquals('1.0.0', $manifest->minimumNexusVersion);
        $this->assertEmpty($manifest->dependencies);
        $this->assertEmpty($manifest->permissions);
        $this->assertNull($manifest->filament);
    }

    public function test_filament_config_parsed(): void
    {
        $manifest = PluginManifest::fromArray([
            'name' => 'filament-plugin',
            'display_name' => 'Filament Plugin',
            'version' => '1.0.0',
            'filament' => [
                'resources' => ['SomeResource'],
                'pages' => ['SomePage'],
                'navigation_group' => 'My Plugin',
                'navigation_group_icon' => 'heroicon-o-puzzle-piece',
            ],
        ]);

        $this->assertNotNull($manifest->filament);
        $this->assertEquals(['SomeResource'], $manifest->filament->resources);
        $this->assertEquals('My Plugin', $manifest->filament->navigationGroup);
    }
}
