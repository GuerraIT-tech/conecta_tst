<x-filament::page>
    <x-filament::section heading="Configurar Radar de Editais">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            Defina a palavra-chave e os filtros. Depois disso, o radar irá atualizar diariamente.
        </div>

        <div class="mt-6">
            {{ $this->form }}
        </div>

        <div class="mt-6 flex justify-end">
            <x-filament::button wire:click="save" color="primary">
                Salvar e continuar
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament::page>
