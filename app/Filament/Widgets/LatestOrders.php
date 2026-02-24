<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool 
    {
        return auth()->user()?->hasPermissionTo('widget.latest_orders.access') || auth()->user()?->hasRole('admin') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(20)
            )
            ->heading('20 Order Terbaru')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order Number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Customer')
                    ->state(fn (Order $record) => $record->user ? $record->user->name : $record->customer_name),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'expired' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('selengkapnya')
                    ->label('Selengkapnya')
                    ->url(route('filament.admin.resources.orders.index'))
                    ->button(),
            ])
            ->paginated(false);
    }
}
