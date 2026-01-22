<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    <div wire:key="order-table-{{ json_encode($data) }}" class="mt-8">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
