@php
    $branches = $this->getBranches();
    $activeBranch = $this->getActiveBranch();
    $lowStockAlert = $activeBranch 
        ? $activeBranch->inventory()
            ->whereColumn('quantity_available', '<=', 'minimum_stock_level')
            ->count() 
        : 0;
@endphp

<x-filament-panels::page>

    {{-- Branch Tabs + Info Card (reusable) --}}
    @include('filament.components.widget-tabs', [
        'branches' => $branches,
        'activeBranch' => $activeBranch,
        'lowStockAlert' => $lowStockAlert,
    ])

    {{-- Stock Table from Filament --}}
    {{ $this->table }}

</x-filament-panels::page>
