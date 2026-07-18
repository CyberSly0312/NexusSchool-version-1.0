<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Support\Facades\Event;

final class PluginEventRegistration
{
    private array $registered = [];

    public function registerEvents(string $pluginName, array $events): void
    {
        foreach ($events as $event => $listeners) {
            $listenerArray = is_array($listeners) ? $listeners : [$listeners];

            foreach ($listenerArray as $listener) {
                if (is_string($listener) && class_exists($listener)) {
                    Event::listen($event, $listener);
                    $this->registered[$pluginName][] = [
                        'event' => $event,
                        'listener' => $listener,
                    ];
                }
            }
        }
    }

    public function unregisterEvents(string $pluginName): void
    {
        unset($this->registered[$pluginName]);
    }

    public function getRegisteredEvents(string $pluginName): array
    {
        return $this->registered[$pluginName] ?? [];
    }

    public function getAllRegistered(): array
    {
        return $this->registered;
    }
}
