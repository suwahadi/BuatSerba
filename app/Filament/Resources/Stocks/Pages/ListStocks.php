<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Models\Branch;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected string $view = 'filament.resources.stocks.pages.list-stocks';

    public ?string $activeTab = null;

    #[\Livewire\Attributes\Locked]
    public string $defaultBranchId;

    public function mount(): void
    {
        parent::mount();

        // Set default tab ke branch pertama yang aktif
        $defaultBranch = Branch::where('is_active', true)
            ->orderBy('priority')
            ->first();

        $this->defaultBranchId = (string) $defaultBranch?->id;
        $this->activeTab = request()->query('branch') ?? $this->defaultBranchId;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Filter berdasarkan branch yang aktif
        if ($this->activeTab) {
            $query = $query->where('branch_id', $this->activeTab);
        }

        return $query;
    }

    public function getActiveBranch(): ?Branch
    {
        return $this->activeTab ? Branch::find($this->activeTab) : null;
    }

    #[On('switch-branch')]
    public function switchBranch(?int $branchId): void
    {
        $this->activeTab = (string) $branchId;
        $this->resetTable();
    }

    public function getBranches()
    {
        return Branch::where('is_active', true)
            ->orderBy('priority')
            ->get();
    }
}

