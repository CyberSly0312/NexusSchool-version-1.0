<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\DTOs\PluginManifest;
use App\Domains\Plugin\DTOs\PluginSettingData;

final class PluginSettingsRegistration
{
    public function registerSettings(PluginManifest $manifest): void
    {
        $configService = app(PluginConfigurationService::class);
        $settings = [];

        foreach ($manifest->settings as $settingData) {
            $data = $settingData instanceof PluginSettingData
                ? $settingData
                : PluginSettingData::fromArray($settingData);

            $settings[] = $data;
        }

        if (!empty($settings)) {
            $configService->setDefaults($manifest->name, $settings);
        }
    }

    public function unregisterSettings(string $pluginName): void
    {
        $configService = app(PluginConfigurationService::class);
        $configService->reset($pluginName);
    }
}
