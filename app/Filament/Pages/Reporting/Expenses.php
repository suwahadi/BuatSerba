<?php

namespace App\Filament\Pages\Reporting;

use App\Models\InternalSale;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class Expenses extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected string $view = 'filament.pages.reporting.expenses';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Pengeluaran';

    protected static ?string $slug = 'reporting/expenses';

    protected static ?string $title = 'Laporan Pengeluaran';

    protected static ?int $navigationSort = 2;

    #[Url(as: 'filters')]
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'finance']);
    }

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('period')
                            ->label('Periode')
                            ->options([
                                'today' => 'Hari Ini',
                                'week' => 'Minggu Ini',
                                'last_week' => 'Minggu Lalu',
                                'month' => 'Bulan Ini',
                                'last_month' => 'Bulan Lalu',
                                'year' => 'Tahun Ini',
                                'last_year' => 'Tahun Lalu',
                                'custom' => 'Range Tanggal',
                            ])
                            ->default('')
                            ->disableOptionWhen(fn (string $value): bool => $value === '')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),

                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->visible(fn (Get $get) => $get('period') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),

                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->visible(fn (Get $get) => $get('period') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn () => $this->redirect(static::getUrl(['filters' => $this->data]))),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InternalSale::query()
                    ->when($this->getDateRange(), function ($query, $range) {
                        [$start, $end] = $range;
                        $query->whereBetween('transaction_date', [$start, $end]);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Information')
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Trx Code')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Qty')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->label('Grand Total')->money('IDR', locale: 'id')),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Input By')
                    ->sortable(),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename('laporan-pengeluaran-'.date('Y-m-d_His'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('name')
                                    ->heading('Information'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('code')
                                    ->heading('Trx Code'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('qty')
                                    ->heading('Qty'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('price')
                                    ->heading('Price'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('total')
                                    ->heading('Total'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('user.name')
                                    ->heading('Input By'),
                            ]),
                    ]),
            ]);
    }

    protected function getDateRange(): array
    {
        $period = $this->data['period'] ?? 'month';

        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            'custom' => [
                isset($this->data['start_date']) ? Carbon::parse($this->data['start_date']) : Carbon::now()->startOfMonth(),
                isset($this->data['end_date']) ? Carbon::parse($this->data['end_date'])->endOfDay() : Carbon::now()->endOfMonth(),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}
