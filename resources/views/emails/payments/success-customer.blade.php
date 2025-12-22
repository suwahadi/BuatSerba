<x-mail::message>
# Pembayaran Berhasil

Halo {{ $order->customer_name }},

Pembayaran atas transaksi Anda telah berhasil dikonfirmasi.

**Nomor Pesanan:** {{ $order->order_number }}  
**Total Pembayaran:** {{ format_rupiah($order->total) }}  
**Tanggal Pembayaran:** {{ $order->paid_at->format('d F Y, H:i') }} WIB

Pesanan Anda sedang diproses dan akan segera dikirimkan.

Terima kasih sudah berbelanja di {{ config('app.name') }}.

---

Jika Anda memiliki pertanyaan, silakan hubungi kami di cs@buatserba.com atau telepon 0800-123-4567.

Salam,  
Tim {{ config('app.name') }}
</x-mail::message>
