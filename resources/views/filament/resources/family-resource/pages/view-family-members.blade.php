<x-filament::page>
    <x-filament::card>
        <h2 class="text-lg font-medium">Family Members for {{ $record->family_code }}</h2>
        {{ $this->table }}
    </x-filament::card>
</x-filament::page>
