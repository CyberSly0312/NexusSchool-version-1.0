<x-filament-panels::page>
    <form wire:submit="import">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" size="lg" color="primary">
                Lancer l'import
            </x-filament::button>

            <x-filament::button tag="a" href="#" size="lg" color="gray" external>
                Télécharger un modèle CSV
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
