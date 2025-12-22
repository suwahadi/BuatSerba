<x-mail::message>
# Pesanan Baru Siap Diproses

Order **{{ $order->order_number }}** telah berhasil divalidasi. Silakan diproses pengiriman.

## Detail Order

**Nomor Pesanan:** {{ $order->order_number }}  
**Tanggal Order:** {{ $order->created_at->format('d F Y, H:i') }} WIB  
**Tanggal Pembayaran:** {{ $order->paid_at->format('d F Y, H:i') }} WIB  
**Status Pembayaran:** Lunas  
**Metode Pembayaran:** {{ ucwords(str_replace('-', ' ', $order->payment_method)) }}

## Detail Pelanggan

**Nama:** {{ $order->customer_name }}  
**Email:** {{ $order->customer_email }}  
**Telepon:** {{ $order->customer_phone }}

## Detail Pengiriman

**Alamat:**  
{{ $order->shipping_address }}  
{{ $order->shipping_subdistrict }}, {{ $order->shipping_district }}  
{{ $order->shipping_city }}, {{ $order->shipping_province }}  
{{ $order->shipping_postal_code }}

**Metode Pengiriman:** {{ $order->shipping_method }}  
**Ongkos Kirim:** {{ format_rupiah($order->shipping_cost) }}

## Produk yang Dipesan

<x-mail::table>
| Produk | SKU | Jumlah | Harga | Subtotal |
|:-------|:----|:------:|------:|---------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->sku_code }} | {{ $item->quantity }} | {{ format_rupiah($item->price) }} | {{ format_rupiah($item->subtotal) }} |
@endforeach
</x-mail::table>

## Ringkasan Pembayaran

**Subtotal Produk:** {{ format_rupiah($order->subtotal) }}  
**Ongkos Kirim:** {{ format_rupiah($order->shipping_cost) }}  
**Biaya Layanan:** {{ format_rupiah($order->service_fee) }}  
@if($order->discount > 0)
**Diskon:** {{ format_rupiah($order->discount) }}  
@endif

**Total Pembayaran:** {{ format_rupiah($order->total) }}

---

<x-mail::button :url="config('app.url') . '/admin/orders/' . $order->id.'/edit'" color="primary">
Lihat Detail Order di Admin
</x-mail::button>

---

Email otomatis dari sistem {{ config('app.name') }}.
</x-mail::message>
