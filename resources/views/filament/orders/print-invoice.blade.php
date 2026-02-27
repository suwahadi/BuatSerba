<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invoice - {{ $record->order_number }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#111827; margin:0; padding:24px; }
        .page { max-width:800px; margin:0 auto; background:#fff; padding:24px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; line-height:1.3em }
        .brand { font-weight:700; font-size:18px; }
        .meta { text-align:right; font-size:14px; }
        .meta .label { color:#6b7280; font-size:12px }
        table { width:100%; border-collapse:collapse; margin-top:18px; }
        th, td { padding:10px 8px; border-bottom:1px solid #e5e7eb; font-size:14px }
        th { text-align:left; background:#f3f4f6; color:#111827 }
        .totals td { border:none; }
        .right { text-align:right }
        .small { font-size:12px; color:#6b7280 }
        .btns { position:fixed; right:24px; top:24px; z-index:9999 }
        .btn { background:#111827; color:#fff; padding:8px 12px; border-radius:6px; text-decoration:none; margin-left:8px; }
        @media print {
            .btns { display:none }
            body { margin:0 }
        }
    </style>
</head>
<body>
<div class="btns">
    <a href="#" class="btn" onclick="window.print();return false">Print / Save PDF</a>
</div>
<div class="page">
    <h3>INVOICE</h3>
    <div class="header">
        <div>
            <div class="brand">{{ global_config('company_name') }}</div>
            <div class="small">{{ global_config('address') }}<br/>{{ global_config('email') }} / {{ global_config('phone') }}</div>
        </div>

        <div class="meta">
            <div style="font-weight:700">{{ $record->order_number }}</div>
            <div class="small">Tanggal: {{ optional($record->created_at)->format('d M Y') }}</div>
            <div class="small">Pembayaran: {{ $payment?->payment_method ?? $record->payment_method ?? '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>SKU</th>
                <th class="right">Jumlah</th>
                <th class="right">Harga Barang</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $record->items ?? collect();
            @endphp
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name ?? $item->product_name ?? '-' }}</td>
                    <td>{{ $item->sku_code ?? '-' }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:12px">
        <tbody>
            <tr>
                <td style="width:60%"></td>
                <td style="width:40%">
                    <table style="width:100%">
                        <tr>
                            <td class="small">Sub Total</td>
                            <td class="right">Rp {{ number_format($record->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="small">Biaya Layanan</td>
                            <td class="right">Rp {{ number_format($record->service_fee ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="small">Ongkos Kirim</td>
                            <td class="right">Rp {{ number_format($record->shipping_cost ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if(($record->discount ?? 0) > 0)
                        <tr>
                            <td class="small" style="color:#dc2626">Diskon / Voucher</td>
                            <td class="right" style="color:#dc2626">- Rp {{ number_format($record->discount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr style="font-weight:700">
                            <td>Total</td>
                            <td class="right">Rp {{ number_format($record->total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    @if($record->voucher_code)
        <div style="margin-top:18px">
            <strong>Voucher & TopPoints (Untuk 2 invoice)</strong>
            <div class="small">{{ $record->voucher_code }} <span style="float:right">(Rp {{ number_format($record->discount ?? 0, 0, ',', '.') }})</span></div>
        </div>
    @endif

    <div style="margin-top:24px" class="small">Terima kasih atas order Anda.</div>
</div>
<script>
    // Auto-print if ?auto=1
    if (new URLSearchParams(window.location.search).get('auto') === '1') {
        setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>
