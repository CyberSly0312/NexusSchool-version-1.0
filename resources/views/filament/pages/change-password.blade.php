<x-filament-panels::page>
    <p class="fi-text-sm">
        Vous pouvez modifier uniquement votre propre mot de passe. Le mot de passe actuel est exigé et aucun mot de passe
        n’est affiché ni transmis à un autre utilisateur.
    </p>

    <form wire:submit="save" class="mt-6">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Modifier mon mot de passe
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
