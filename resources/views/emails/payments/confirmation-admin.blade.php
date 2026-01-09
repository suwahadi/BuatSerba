<x-mail::message>
# Konfirmasi Pembayaran Manual

Pelanggan telah melakukan konfirmasi pembayaran manual untuk pesanan **{{ $order->order_number }}**.

## Detail Konfirmasi

**Nama Pengirim:** {{ $data['sender_name'] }}  
**Bank Pengirim:** {{ $data['sender_bank'] }}  
**Nomor Rekening:** {{ $data['sender_account_number'] }}  
**Catatan:** {{ $data['notes'] ?? '-' }}

## Detail Order

**Nomor Pesanan:** {{ $order->order_number }}  
**Total Tagihan:** {{ format_rupiah($order->total) }}  
**Tanggal Order:** {{ $order->created_at->format('d F Y, H:i') }} WIB

**Bukti transfer bisa dilihat di: ** [Lihat Bukti Transfer]({{ $data['proof_url'] }})

---

<x-mail::button :url="config('app.url') . '/admin/orders/' . $order->id . '/edit'" color="primary">
Cek Order di Admin
</x-mail::button>

Silakan validasi pembayaran ini di rekening tujuan dan update status pembayaran pesanan di admin panel jika dana sudah diterima.

Email otomatis dari sistem {{ config('app.name') }}.
</x-mail::message>
