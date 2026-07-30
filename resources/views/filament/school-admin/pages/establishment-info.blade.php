<x-filament-panels::page
    @if (method_exists($this, 'getFormActions'))
        :form-actions="$this->getFormActions()"
    @endif
>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Enregistrer
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
