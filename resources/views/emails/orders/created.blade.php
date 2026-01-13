<x-mail::message>
# Pesanan Berhasil Dibuat

Terima kasih atas pesanan Anda. Berikut adalah detail pesanan Anda:

## Informasi Pesanan

**Nomor Pesanan:** {{ $order->order_number }}  
**Tanggal:** {{ $order->created_at->format('d F Y, H:i') }} WIB  
**Status:** Menunggu Pembayaran

## Detail Pelanggan

**Nama:** {{ $order->customer_name }}  
**Email:** {{ $order->customer_email }}  
**Telepon:** {{ $order->customer_phone }}

## Alamat Pengiriman

{{ $order->shipping_address }}  
{{ $order->shipping_subdistrict }}, {{ $order->shipping_district }}  
{{ $order->shipping_city }}, {{ $order->shipping_province }}  
{{ $order->shipping_postal_code }}

## Produk yang Dipesan

<x-mail::table>
| Produk | Jumlah | Harga Satuan | Subtotal |
|:-------|:------:|-------------:|---------:|
@foreach($order->items as $item)
| {{ $item->product_name }}<br><small>{{ $item->sku_code }}</small> | {{ $item->quantity }} | {{ format_rupiah($item->price) }} | {{ format_rupiah($item->subtotal) }} |
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

## Metode Pembayaran

{{ ucwords(str_replace('-', ' ', $order->payment_method)) }}

Batas waktu pembayaran: **{{ $order->payment_deadline->format('d F Y, H:i') }} WIB**

<x-mail::button :url="$paymentUrl" color="success">
Bayar Sekarang
</x-mail::button>

Atau salin tautan berikut ke browser Anda:  
{{ $paymentUrl }}

---

Jika Anda memiliki pertanyaan, silakan hubungi kami di {{ global_config('email') }} atau telepon/whatsapp {{ global_config('phone') }}.

Terima kasih telah berbelanja di {{ global_config('site_name') }}.

Salam,  
Tim {{ global_config('site_name') }}
</x-mail::message>
