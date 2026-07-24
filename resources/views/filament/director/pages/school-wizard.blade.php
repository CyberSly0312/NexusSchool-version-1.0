<x-filament-panels::page>
    <div class="mb-6">
        <div class="flex items-center justify-center space-x-4">
            @for ($i = 1; $i <= $this->totalSteps; $i++)
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold
                        {{ $this->currentStep >= $i
                            ? 'bg-primary-500 text-white'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                        {{ $i }}
                    </div>
                    @if ($i < $this->totalSteps)
                        <div class="w-12 h-0.5 mx-2
                            {{ $this->currentStep > $i ? 'bg-primary-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                        </div>
                    @endif
                </div>
            @endfor
        </div>
        <div class="flex justify-center mt-2 space-x-8 text-xs text-gray-500">
            <span>Général</span>
            <span>Académique</span>
            <span>Finance</span>
            <span>Identité</span>
            <span>Vérification</span>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-between">
            @if ($this->currentStep > 1)
                <x-filament::button
                    type="button"
                    wire:click="previousStep"
                    color="gray"
                    outlined>
                    Précédent
                </x-filament::button>
            @else
                <div></div>
            @endif

            @if ($this->currentStep < $this->totalSteps)
                <x-filament::button
                    type="button"
                    wire:click="nextStep">
                    Suivant
                </x-filament::button>
            @else
                <x-filament::button type="submit">
                    Enregistrer la configuration
                </x-filament::button>
            @endif
        </div>
    </form>
</x-filament-panels::page>
