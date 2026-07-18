<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\DTOs\PluginSettingData;
use App\Models\Plugin;
use App\Models\PluginSetting;
use Illuminate\Support\Collection;

final class PluginConfigurationService
{
    public function get(string $pluginName, string $key, ?string $schoolId = null): mixed
    {
        $plugin = $this->getPlugin($pluginName);
        if ($plugin === null) {
            return null;
        }

        $query = PluginSetting::forPlugin($plugin->id)->forKey($key);

        if ($schoolId !== null) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                    ->orWhereNull('school_id');
            });
        } else {
            $query->whereNull('school_id');
        }

        $setting = $query->orderBy('school_id', 'desc')->first();

        return $setting?->value;
    }

    public function set(string $pluginName, string $key, mixed $value, ?string $schoolId = null): void
    {
        $plugin = $this->getPlugin($pluginName);
        if ($plugin === null) {
            return;
        }

        PluginSetting::updateOrCreate(
            [
                'plugin_id' => $plugin->id,
                'school_id' => $schoolId,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }

    public function getMany(string $pluginName, ?string $schoolId = null): array
    {
        $plugin = $this->getPlugin($pluginName);
        if ($plugin === null) {
            return [];
        }

        $query = PluginSetting::forPlugin($plugin->id);

        if ($schoolId !== null) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                    ->orWhereNull('school_id');
            });
        } else {
            $query->whereNull('school_id');
        }

        $settings = $query->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }

    public function setDefaults(string $pluginName, array $settings): void
    {
        $plugin = $this->getPlugin($pluginName);
        if ($plugin === null) {
            return;
        }

        foreach ($settings as $settingData) {
            $data = $settingData instanceof PluginSettingData
                ? $settingData
                : PluginSettingData::fromArray($settingData);

            $existing = PluginSetting::forPlugin($plugin->id)
                ->forKey($data->key)
                ->whereNull('school_id')
                ->first();

            if ($existing === null) {
                PluginSetting::create([
                    'plugin_id' => $plugin->id,
                    'school_id' => null,
                    'key' => $data->key,
                    'value' => $data->default,
                    'type' => $data->type,
                ]);
            }
        }
    }

    public function reset(string $pluginName, ?string $schoolId = null): void
    {
        $plugin = $this->getPlugin($pluginName);
        if ($plugin === null) {
            return;
        }

        $query = PluginSetting::forPlugin($plugin->id);

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        } else {
            $query->whereNull('school_id');
        }

        $query->delete();
    }

    private function getPlugin(string $name): ?Plugin
    {
        return Plugin::where('name', $name)->first();
    }
}
