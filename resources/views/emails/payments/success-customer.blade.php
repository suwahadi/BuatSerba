<x-mail::message>
# Pembayaran Berhasil

Halo {{ $order->customer_name }},

Pembayaran atas transaksi Anda telah berhasil dikonfirmasi.

**Nomor Pesanan:** {{ $order->order_number }}  
**Total Pembayaran:** {{ format_rupiah($order->total) }}  
**Tanggal Pembayaran:** {{ $order->paid_at->format('d F Y, H:i') }} WIB

Pesanan Anda sedang diproses dan akan segera dikirimkan.

Terima kasih sudah berbelanja di {{ global_config('site_name') }}.

---

Jika Anda memiliki pertanyaan, silakan hubungi kami di {{ global_config('email') }} atau telepon/whatsapp {{ global_config('phone') }}.

Salam,  
Tim {{ global_config('site_name') }}
</x-mail::message>
