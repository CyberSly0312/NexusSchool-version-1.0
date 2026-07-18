<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use App\Domains\Plugin\DTOs\PluginMenuData;
use App\Domains\Plugin\DTOs\PluginManifest;
use Illuminate\Support\Facades\Blade;

final class PluginMenuRegistration
{
    private array $registeredMenus = [];

    public function registerMenus(PluginManifest $manifest): void
    {
        $menus = $manifest->menus;

        foreach ($menus as $menuData) {
            $menu = $menuData instanceof PluginMenuData
                ? $menuData
                : PluginMenuData::fromArray($menuData);

            $this->registeredMenus[$manifest->name][] = $menu;
        }
    }

    public function unregisterMenus(string $pluginName): void
    {
        unset($this->registeredMenus[$pluginName]);
    }

    public function getMenus(string $pluginName): array
    {
        return $this->registeredMenus[$pluginName] ?? [];
    }

    public function getAllMenus(): array
    {
        return $this->registeredMenus;
    }
}
