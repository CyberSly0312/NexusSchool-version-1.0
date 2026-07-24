<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Générer un document
            </x-slot>

            <form wire:submit="generateDocument">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                        Générer le PDF
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Documents récents
            </x-slot>

            @php $documents = $this->getRecentDocuments(); @endphp

            @if($documents->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    Aucun document généré récemment.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Référence</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Élève</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Signé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium">{{ $doc->reference_number }}</td>
                                    <td class="px-4 py-3">{{ $doc->type instanceof \BackedEnum ? $doc->type->label() : $doc->type }}</td>
                                    <td class="px-4 py-3">{{ $doc->student?->student_number ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        @if($doc->signed_at)
                                            <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-500" />
                                        @else
                                            <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5 text-warning-500" />
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
