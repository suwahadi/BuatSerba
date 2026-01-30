<x-filament-panels::page>
    <style>
        .opname-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .dark .opname-card {
            background: #1f2937;
            border-color: #374151;
        }
        .opname-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .dark .opname-card-header {
            background: #111827;
            border-color: #374151;
        }
        .opname-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .dark .opname-card-title {
            color: #f9fafb;
        }
        .opname-card-body {
            padding: 1.5rem;
        }
        .opname-alert-success {
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .dark .opname-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .opname-alert-warning {
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .dark .opname-alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }
        .opname-info-row {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .opname-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .opname-info-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .opname-info-label {
            color: #9ca3af;
        }
        .opname-info-value {
            font-size: 0.75rem;
            color: #333;
        }
        .dark .opname-info-value {
            color: #f9fafb;
        }
        .opname-table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f3f4f6;
        }
        .opname-table-scroll::-webkit-scrollbar {
            height: 8px;
        }
        .opname-table-scroll::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        .opname-table-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        .opname-table-scroll::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        .dark .opname-table-scroll {
            scrollbar-color: #4b5563 #1f2937;
        }
        .dark .opname-table-scroll::-webkit-scrollbar-track {
            background: #1f2937;
        }
        .dark .opname-table-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        .opname-table {
            width: 100%;
            min-width: 700px;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .opname-table th {
            background: #f9fafb;
            padding: 0.875rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .dark .opname-table th {
            background: #111827;
            color: #9ca3af;
            border-color: #374151;
        }
        .opname-table th.text-right {
            text-align: right;
        }
        .opname-table td {
            padding: 0.875rem 1rem;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            white-space: nowrap;
        }
        .dark .opname-table td {
            color: #f9fafb;
            border-color: #374151;
        }
        .opname-table td.text-right {
            text-align: right;
        }
        .opname-table tr:last-child td {
            border-bottom: none;
        }
        .opname-table tr:hover td {
            background: #f9fafb;
        }
        .dark .opname-table tr:hover td {
            background: rgba(55, 65, 81, 0.5);
        }
        .opname-product-name {
            font-weight: 500;
        }
        .opname-product-sku {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .dark .opname-product-sku {
            color: #9ca3af;
        }
        .opname-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            white-space: nowrap;
        }
        .opname-badge-success {
            background: #ecfdf5;
            color: #059669;
        }
        .dark .opname-badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        .opname-badge-warning {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fcd34d;
        }
        .dark .opname-badge-warning {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border-color: rgba(251, 191, 36, 0.3);
        }
        .opname-difference-negative {
            color: #dc2626;
            font-weight: 500;
        }
        .dark .opname-difference-negative {
            color: #f87171;
        }
        .opname-difference-positive {
            color: #059669;
            font-weight: 500;
        }
        .dark .opname-difference-positive {
            color: #34d399;
        }
        .opname-new-stock {
            color: #059669;
            font-weight: 600;
        }
        .dark .opname-new-stock {
            color: #34d399;
        }
        .opname-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
        .opname-empty svg {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
        }
    </style>

    @php
        $record = $this->getRecord();
    @endphp

    <div class="opname-card">
        <div class="opname-card-header">
            <h3 class="opname-card-title">Stok Opname: {{ $record->notes ?: '-' }}</h3>
        </div>
        <div class="opname-card-body">
            <div class="opname-info-row">
                <div class="opname-info-item">
                    <span class="opname-info-label">Tanggal</span>
                    <span class="opname-info-value">{{ $record->opname_date->format('d/m/Y') }}</span>
                </div>
                <div class="opname-info-item">
                    <span class="opname-info-label">Staff</span>
                    <span class="opname-info-value">{{ $record->user->name }}</span>
                </div>
            </div>

            @if($record->is_adjusted)
                <div class="opname-alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Stok telah disinkronisasikan oleh {{ $record->user->name }} pada: {{ $record->adjusted_at->format('d/m/Y H:i:s') }}
                </div>
            @else
                <div class="opname-alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 4v6h-6"></path>
                        <path d="M1 20v-6h6"></path>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    Stok belum disinkronisasikan...
                </div>
            @endif

            <div class="opname-table-scroll">
                <table class="opname-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th class="text-right">Stok Sistem</th>
                            <th class="text-right">Stok Fisik</th>
                            <th class="text-right">Selisih</th>
                            <th class="text-right">Stok Sistem Baru</th>
                            <th class="text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($record->items as $item)
                            <tr>
                                <td><span class="opname-product-sku">{{ $item->sku->sku }}</span></td>
                                <td><span class="opname-product-name">{{ $item->sku->product->name }}</span></td>
                                <td class="text-right">{{ number_format($item->system_stock) }}</td>
                                <td class="text-right">{{ number_format($item->physical_stock) }}</td>
                                <td class="text-right">
                                    <span class="{{ $item->difference < 0 ? 'opname-difference-negative' : ($item->difference > 0 ? 'opname-difference-positive' : '') }}">
                                        {{ $item->difference > 0 ? '+' : '' }}{{ number_format($item->difference) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($item->is_adjusted)
                                        <span class="opname-new-stock">{{ number_format($item->new_system_stock) }}</span>
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($item->is_adjusted)
                                        <span class="opname-badge opname-badge-success">Sesuai</span>
                                    @elseif($item->difference != 0)
                                        <span class="opname-badge opname-badge-warning">Belum Sesuai</span>
                                    @else
                                        <span class="opname-badge opname-badge-success">Sesuai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="opname-empty">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                        <p>Belum ada item</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
