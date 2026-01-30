<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use App\Models\Sku;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateStockOpname extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StockOpnameResource::class;

    protected string $view = 'filament.resources.stock-opnames.pages.create-stock-opname';

    protected static ?string $title = 'Tambah Stok Opname';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'opname_date' => now()->format('Y-m-d'),
            'notes' => '',
            'items' => [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('opname_date')
                    ->label('Tanggal Opname')
                    ->required()
                    ->default(now()),
                Textarea::make('notes')
                    ->label('Keterangan')
                    ->rows(2)
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label('Daftar Barang')
                    ->schema([
                        Select::make('sku_id')
                            ->label('Produk')
                            ->options(function () {
                                return Sku::with('product')
                                    ->get()
                                    ->mapWithKeys(fn ($sku) => [$sku->id => $sku->product->name.' - '.$sku->sku]);
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $sku = Sku::find($state);
                                    if ($sku) {
                                        $set('system_stock', $sku->stock_quantity);
                                    }
                                }
                            })
                            ->columnSpan(3),
                        TextInput::make('system_stock')
                            ->label('Stok Sistem')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->columnSpan(1),
                        TextInput::make('physical_stock')
                            ->label('Stok Fisik')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live(debounce: 300)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $systemStock = (int) $get('system_stock');
                                $physicalStock = (int) $get('physical_stock');
                                $set('difference', $physicalStock - $systemStock);
                            })
                            ->columnSpan(1),
                        TextInput::make('difference')
                            ->label('Selisih')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->columnSpan(1),
                    ])
                    ->columns(6)
                    ->addActionLabel('Tambah Barang')
                    ->columnSpanFull()
                    ->minItems(1),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            $stockOpname = StockOpname::create([
                'user_id' => Auth::id(),
                'opname_date' => $data['opname_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $stockOpname->id,
                    'sku_id' => $item['sku_id'],
                    'system_stock' => $item['system_stock'],
                    'physical_stock' => $item['physical_stock'],
                    'difference' => $item['difference'],
                ]);
            }
        });

        Notification::make()
            ->title('Stok Opname berhasil dibuat')
            ->success()
            ->send();

        $this->redirect(StockOpnameResource::getUrl('index'));
    }
}
