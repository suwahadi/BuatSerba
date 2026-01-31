<x-filament-panels::page>
    <style>
        .receipt-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            font-family: 'Courier New', Courier, monospace;
        }
        .receipt-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .dark .receipt-card {
            background: #1f2937;
            border-color: #374151;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 1rem;
        }
        .receipt-store-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .dark .receipt-store-name {
            color: #f9fafb;
        }
        .receipt-store-info {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0.25rem 0;
        }
        .receipt-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            margin-top: 0.75rem;
        }
        .dark .receipt-title {
            color: #f9fafb;
        }
        .receipt-divider {
            border: none;
            border-top: 1px dashed #9ca3af;
            margin: 0.75rem 0;
        }
        .receipt-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }
        .dark .receipt-meta {
            color: #9ca3af;
        }
        .receipt-order-number {
            text-align: center;
            font-size: 0.875rem;
            font-weight: 700;
            color: #059669;
            margin: 0.5rem 0;
        }
        .receipt-item {
            margin-bottom: 0.75rem;
        }
        .receipt-item-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .dark .receipt-item-name {
            color: #f9fafb;
        }
        .receipt-item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #6b7280;
        }
        .receipt-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
        }
        .dark .receipt-summary-row {
            color: #9ca3af;
        }
        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            padding-top: 0.5rem;
            border-top: 1px solid #e5e7eb;
            margin-top: 0.5rem;
        }
        .dark .receipt-total-row {
            color: #f9fafb;
            border-color: #374151;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 1rem;
        }
        .receipt-footer-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0;
        }
        .receipt-footer-date {
            font-size: 0.65rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }
        .receipt-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .receipt-btn {
            flex: 1;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
        }
        .receipt-btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .receipt-btn-secondary:hover {
            background: #e5e7eb;
        }
        .dark .receipt-btn-secondary {
            background: #374151;
            color: #f9fafb;
        }
        .dark .receipt-btn-secondary:hover {
            background: #4b5563;
        }
        .receipt-btn-primary {
            background: #10b981;
            color: white;
        }
        .receipt-btn-primary:hover {
            background: #059669;
        }
        @media print {
            body * { visibility: hidden; }
            .receipt-container, .receipt-container * { visibility: visible; }
            .receipt-container { position: absolute; left: 0; top: 0; width: 80mm; }
            .receipt-buttons { display: none !important; }
            .receipt-card { box-shadow: none; border: none; }
        }
    </style>

    <div class="receipt-container">
        <div class="receipt-card">
            <div class="receipt-header">
                <p class="receipt-title">## Struk Pembelian ##</p>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-meta">
                <div>
                    <p style="margin: 0;">{{ $order->created_at->format('d M Y') }}</p>
                    <p style="margin: 0;">{{ $order->created_at->format('H:i:s') }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0;">{{ $order->customer_name }}</p>
                    <p style="margin: 0; font-size: 0.65rem; color: #9ca3af;">Kasir: {{ $order->user?->name ?? 'Kasir' }}</p>
                </div>
            </div>

            <p class="receipt-order-number">{{ $order->order_number }}</p>

            <hr class="receipt-divider">

            @foreach($order->items as $item)
            <div class="receipt-item">
                <p class="receipt-item-name">{{ $item->product_name }}</p>
                <div class="receipt-item-detail">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach

            <hr class="receipt-divider">

            <div class="receipt-summary-row">
                <span>Sub Total</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="receipt-summary-row">
                <span>Diskon</span>
                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="receipt-total-row">
                <span>Total</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-summary-row">
                <span>Bayar (Cash)</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-summary-row">
                <span>Kembali</span>
                <span>Rp 0</span>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-footer">
                <p class="receipt-footer-text">Terima kasih atas kunjungan Anda!</p>
                <p class="receipt-footer-date">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
            </div>

            <div class="receipt-buttons">
                <a href="{{ route('filament.admin.pages.pos') }}" class="receipt-btn receipt-btn-secondary">
                    Kembali
                </a>
                <button onclick="window.print()" class="receipt-btn receipt-btn-primary">
                    Cetak
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
