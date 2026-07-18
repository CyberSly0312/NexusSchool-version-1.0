<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Services\PluginConfigurationService;
use App\Domains\Plugin\Services\PluginRegistry;
use App\Models\Plugin;
use App\Models\PluginSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PluginConfigurationServiceTest extends TestCase
{
    use RefreshDatabase;

    private PluginConfigurationService $configService;
    private PluginRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configService = new PluginConfigurationService();
        $this->registry = new PluginRegistry();

        $manifest = PluginManifest::fromArray([
            'name' => 'config-test-plugin',
            'display_name' => 'Config Test Plugin',
            'version' => '1.0.0',
        ]);
        $this->registry->register('config-test-plugin', $manifest);
    }

    public function test_can_set_and_get_value(): void
    {
        $this->configService->set('config-test-plugin', 'api_key', 'secret-123');

        $value = $this->configService->get('config-test-plugin', 'api_key');
        $this->assertEquals('secret-123', $value);
    }

    public function test_can_get_multiple_values(): void
    {
        $this->configService->set('config-test-plugin', 'key1', 'value1');
        $this->configService->set('config-test-plugin', 'key2', 'value2');

        $values = $this->configService->getMany('config-test-plugin');
        $this->assertEquals('value1', $values['key1']);
        $this->assertEquals('value2', $values['key2']);
    }

    public function test_can_reset_settings(): void
    {
        $this->configService->set('config-test-plugin', 'key1', 'value1');
        $this->configService->reset('config-test-plugin');

        $values = $this->configService->getMany('config-test-plugin');
        $this->assertEmpty($values);
    }

    public function test_returns_null_for_unknown_plugin(): void
    {
        $value = $this->configService->get('nonexistent', 'key');
        $this->assertNull($value);
    }

    public function test_can_overwrite_value(): void
    {
        $this->configService->set('config-test-plugin', 'key', 'old');
        $this->configService->set('config-test-plugin', 'key', 'new');

        $value = $this->configService->get('config-test-plugin', 'key');
        $this->assertEquals('new', $value);
    }
}
