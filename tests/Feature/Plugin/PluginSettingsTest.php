<?php
declare(strict_types=1);

namespace Tests\Feature\Plugin;

use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\Services\PluginConfigurationService;
use App\Domains\Plugin\Services\PluginRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PluginSettingsTest extends TestCase
{
    use RefreshDatabase;

    private PluginConfigurationService $configService;
    private PluginRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configService = app(PluginConfigurationService::class);
        $this->registry = app(PluginRegistry::class);

        $this->registry->register('settings-test', PluginManifest::fromArray([
            'name' => 'settings-test',
            'display_name' => 'Settings Test',
            'version' => '1.0.0',
            'settings' => [
                ['key' => 'color', 'label' => 'Color', 'type' => 'text', 'default' => 'blue'],
                ['key' => 'size', 'label' => 'Size', 'type' => 'number', 'default' => 10],
            ],
        ]));
    }

    public function test_can_store_and_retrieve_settings(): void
    {
        $this->configService->set('settings-test', 'color', 'red');

        $value = $this->configService->get('settings-test', 'color');
        $this->assertEquals('red', $value);
    }

    public function test_can_store_complex_values(): void
    {
        $complex = ['enabled' => true, 'items' => ['a', 'b', 'c']];
        $this->configService->set('settings-test', 'config', $complex);

        $value = $this->configService->get('settings-test', 'config');
        $this->assertEquals($complex, $value);
    }

    public function test_can_get_all_settings(): void
    {
        $this->configService->set('settings-test', 'color', 'green');
        $this->configService->set('settings-test', 'font', 'Arial');

        $all = $this->configService->getMany('settings-test');
        $this->assertEquals('green', $all['color']);
        $this->assertEquals('Arial', $all['font']);
    }
}
