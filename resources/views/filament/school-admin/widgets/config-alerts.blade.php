<x-filament-widgets::widget>
    @if (count($this->alerts) > 0)
        <div class="fi-wi-stats-overview-widget rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="mb-4 flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Alertes de configuration</h3>
            </div>
            <div class="space-y-2">
                @foreach ($this->alerts as $alert)
                    <div class="flex items-center gap-2 rounded-lg p-2 @if ($alert['type'] === 'danger') bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400 @else bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 @endif">
                        <x-heroicon-s-exclamation-triangle class="h-4 w-4 shrink-0" />
                        <span class="text-sm">{{ $alert['message'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <x-filament-widgets::widget>
            <div class="fi-wi-stats-overview-widget rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <div class="flex items-center gap-2 text-success-600 dark:text-success-400">
                    <x-heroicon-o-check-circle class="h-5 w-5" />
                    <span class="text-sm font-semibold">Toute la configuration est à jour</span>
                </div>
            </div>
        </x-filament-widgets::widget>
    @endif
</x-filament-widgets::widget>
