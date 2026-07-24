@if (count($this->alerts) > 0)
    <x-filament-widgets::widget>
        <x-filament::section heading="Alertes de configuration" icon="heroicon-o-exclamation-triangle">
            <div class="space-y-3">
                @foreach ($this->alerts as $alert)
                    <div @class([
                        'flex items-start gap-3 rounded-lg border p-4',
                        'border-danger-200 bg-danger-50 dark:border-danger-700 dark:bg-danger-950' => $alert['type'] === 'danger',
                        'border-warning-200 bg-warning-50 dark:border-warning-700 dark:bg-warning-950' => $alert['type'] === 'warning',
                        'border-info-200 bg-info-50 dark:border-info-700 dark:bg-info-950' => $alert['type'] === 'info',
                    ])>
                        <x-heroicon-o-exclamation-triangle @class([
                            'mt-0.5 h-5 w-5 shrink-0',
                            'text-danger-600 dark:text-danger-400' => $alert['type'] === 'danger',
                            'text-warning-600 dark:text-warning-400' => $alert['type'] === 'warning',
                            'text-info-600 dark:text-info-400' => $alert['type'] === 'info',
                        ]) />
                        <div class="flex-1">
                            <p @class([
                                'text-sm font-semibold',
                                'text-danger-700 dark:text-danger-300' => $alert['type'] === 'danger',
                                'text-warning-700 dark:text-warning-300' => $alert['type'] === 'warning',
                                'text-info-700 dark:text-info-300' => $alert['type'] === 'info',
                            ])>{{ $alert['title'] }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $alert['message'] }}</p>
                        </div>
                        <a href="{{ url('/school-admin/' . $alert['action']) }}" class="shrink-0 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                            Configurer
                        </a>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
