<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Resi - {{ $record->order_number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#000; margin:0; padding:12px; background:#fff }
        .resi { max-width:400px; margin:0 auto; background:#fff; border:2px solid #000; padding:0; }
        .header { background:#fff; padding:8px; border-bottom:2px solid #000; text-align:center; }
        .header-brand { font-weight:700; font-size:16px; margin:0; }
        .header-sub { font-size:12px; margin:2px 0 0 0; }
        .section { padding:8px; border-bottom:1px solid #000; }
        .section-title { font-weight:700; font-size:12px; margin-bottom:4px; text-transform:uppercase; }
        .barcode-container { text-align:center; padding:8px 0; border-bottom:1px solid #000; }
        svg { max-width:100%; height:auto; }
        .awb-number { font-size:18px; font-weight:700; text-align:center; padding:8px; border-bottom:1px solid #000; line-height:1.2; }
        .info-row { display:flex; justify-content:space-between; font-size:11px; margin:2px 0; }
        .info-label { font-weight:700; }
        .address-section { font-size:11px; line-height:1.4; margin-bottom:8px; }
        .address-name { font-weight:700; }
        .btns { position:fixed; right:12px; top:12px; z-index:9999 }
        .btn { background:#000; color:#fff; padding:8px 12px; border-radius:4px; text-decoration:none; margin-left:8px; font-size:12px; }
        @media print {
            .btns { display:none }
            body { margin:0; padding:0 }
            .resi { max-width:100%; border:none; margin:0 }
        }
    </style>
</head>
<body>
<div class="btns">
    <a href="#" class="btn" onclick="window.print();return false">Print / Save PDF</a>
</div>

<div class="resi">
    <div class="header">
        <h2 class="header-brand">{{ global_config('company_name') ?? 'PERUSAHAAN' }}</h2>
        <p class="header-sub">RESI PENGIRIMAN</p>
    </div>

    <div class="awb-number">
        {{ $record->order_number }}
    </div>

    <div class="barcode-container">
        <svg id="barcode"></svg>
    </div>

    <div class="section">
        <div class="info-row">
            <span class="info-label">Layanan:</span>
            <span>{{ strtoupper($record->shipping_method ?? 'SiCepat') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Qty:</span>
            <span>{{ number_format($record->items?->sum('quantity') ?? 1) }} pcs</span>
        </div>
        <div class="info-row">
            <span class="info-label">Berat:</span>
            <span>{{ number_format($record->items?->sum(fn($item) => ($item->sku?->weight ?? 0) * $item->quantity) ?? 200) }} gram</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ongkir:</span>
            <span>Rp {{ number_format($record->shipping_cost ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Penerima</div>
        <div class="address-section">
            <div class="address-name">{{ $record->customer_name }}</div>
            <div>{{ $record->shipping_address }}</div>
            <div>{{ $record->customer_phone }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Pengirim</div>
        <div class="address-section">
            <div class="address-name">{{ global_config('company_name') }}</div>
            <div>{{ global_config('address') }}</div>
            <div>{{ global_config('phone') }}</div>
        </div>
    </div>

    <div style="padding:8px; border-bottom:1px solid #000; font-size:10px; color:#666;">
        <div style="margin-bottom:4px;">{{ now()->format('d M Y H:i') }}</div>
    </div>

</div>

<script>
    // Generate barcode from order number
    JsBarcode("#barcode", "{{ $record->order_number }}", {
        format: "CODE128",
        width: 2,
        height: 50,
        displayValue: false
    });

    // Auto-print if ?auto=1
    if (new URLSearchParams(window.location.search).get('auto') === '1') {
        setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>
