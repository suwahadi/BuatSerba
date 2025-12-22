# Payment Success Email Notification

## Overview

Sistem secara otomatis mengirim email notifikasi saat pembayaran berhasil dikonfirmasi oleh Midtrans. Email dikirim ke:
1. **Customer** - Konfirmasi pembayaran berhasil
2. **Admin** - Notifikasi pesanan baru yang siap diproses

## Email Templates

### 1. Email Customer (Simple & Clean)

**Subject:** Pembayaran Berhasil: {ORDER-NUMBER}

**Content:**
- Greeting dengan nama customer
- Konfirmasi pembayaran berhasil
- Nomor pesanan
- Total pembayaran
- Tanggal pembayaran
- Status pesanan (sedang diproses)
- Contact support

### 2. Email Admin (Detailed)

**Subject:** Pesanan Baru Siap Diproses: {ORDER-NUMBER}

**Content:**
- Informasi order lengkap (nomor, tanggal, status)
- Detail pelanggan (nama, email, telepon)
- Alamat pengiriman lengkap
- Tabel produk yang dipesan
- Ringkasan pembayaran (subtotal, ongkir, total)
- Tombol link ke admin panel untuk proses order

## File Structure

```
app/
├── Mail/
│   ├── PaymentSuccessCustomer.php
│   └── PaymentSuccessAdmin.php
├── Jobs/
│   └── SendPaymentSuccessEmail.php
└── Http/Controllers/
    └── MidtransController.php (updated)

resources/views/emails/payments/
├── success-customer.blade.php
└── success-admin.blade.php

config/
└── mail.php (added admin_email config)
```

## Configuration

### Environment Variables

Add to `.env`:

```env
MAIL_ADMIN=admin@buatserba.com
```

This email will receive notifications for:
- New paid orders
- Critical system alerts
- Important updates

## How It Works

### Flow Diagram

```
Midtrans Payment Success Callback
        ↓
MidtransController::notification()
        ↓
Update Order Status to "paid"
        ↓
Dispatch SendPaymentSuccessEmail Job
        ↓
Queue Worker Processes Job
        ↓
Send Email to Customer & Admin
```

### Code Flow

1. **Midtrans Callback** (`MidtransController.php`)
   ```php
   // After payment status update
   if ($order->payment_status === 'paid') {
       \App\Jobs\SendPaymentSuccessEmail::dispatch($order);
   }
   ```

2. **Job Processing** (`SendPaymentSuccessEmail.php`)
   ```php
   // Send to customer
   Mail::to($order->customer_email)
       ->send(new PaymentSuccessCustomer($order));
   
   // Send to admin
   $adminEmail = config('mail.admin_email');
   Mail::to($adminEmail)
       ->send(new PaymentSuccessAdmin($order));
   ```

3. **Email Rendering**
   - Uses Laravel Markdown mail components
   - Clean, simple design
   - No background gradients
   - No emojis
   - All text in Indonesian

## Testing

### Test via Tinker

```bash
php artisan tinker
```

```php
use App\Models\Order;
use App\Jobs\SendPaymentSuccessEmail;

// Get paid order
$order = Order::where('payment_status', 'paid')->first();

// Dispatch job
SendPaymentSuccessEmail::dispatch($order);

// Exit tinker
exit;
```

Then run queue worker:
```bash
php artisan queue:work --once
```

### Test Midtrans Callback

1. Use Midtrans dashboard to send test notification
2. Or use cURL to simulate callback:

```bash
curl -X POST http://your-domain.com/midtrans/notification \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "ORD-20251222-ABC123",
    "transaction_status": "settlement",
    "status_code": "200",
    "gross_amount": "100000",
    "signature_key": "..."
  }'
```

## Monitoring

### Check Queued Jobs

```bash
# View queued jobs
php artisan queue:listen --timeout=0

# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all
```

### Check Logs

```bash
# Email sending logs
tail -f storage/logs/laravel.log | grep "Payment success email"

# Payment notification logs
tail -f storage/logs/laravel.log | grep "Payment notification"
```

## Email Design

### Customer Email Preview

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# Pembayaran Berhasil

Halo John Doe,

Pembayaran atas transaksi Anda telah 
berhasil dikonfirmasi.

Nomor Pesanan: ORD-20251222-ABC123
Total Pembayaran: Rp 150.000
Tanggal Pembayaran: 22 Desember 2025, 14:30 WIB

Pesanan Anda sedang diproses dan akan 
segera dikirimkan.

Terima kasih sudah berbelanja di BuatSerba.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Jika Anda memiliki pertanyaan, silakan 
hubungi kami di cs@buatserba.com atau 
telepon 0800-123-4567.

Salam,
Tim BuatSerba
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Admin Email Preview

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# Pesanan Baru Siap Diproses

Order ORD-20251222-ABC123 telah berhasil 
divalidasi. Silakan diproses pengiriman.

## Detail Order
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Nomor Pesanan: ORD-20251222-ABC123
Tanggal Order: 22 Des 2025, 14:00 WIB
Tanggal Pembayaran: 22 Des 2025, 14:30 WIB
Status Pembayaran: Lunas
Metode Pembayaran: BCA Virtual Account

## Detail Pelanggan
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Nama: John Doe
Email: john@example.com
Telepon: 08123456789

## Detail Pengiriman
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Alamat:
Jl. Kebon Jeruk No. 123
KEBON JERUK, KEBON JERUK
JAKARTA BARAT, DKI JAKARTA
11530

Metode Pengiriman: JNE REG
Ongkos Kirim: Rp 15.000

## Produk yang Dipesan
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
| Produk    | SKU     | Qty | Harga      | Subtotal    |
|-----------|---------|-----|------------|-------------|
| Product A | SKU-001 | 2   | Rp 50.000  | Rp 100.000  |
| Product B | SKU-002 | 1   | Rp 35.000  | Rp 35.000   |

## Ringkasan Pembayaran
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Subtotal Produk: Rp 135.000
Ongkos Kirim: Rp 15.000
Biaya Layanan: Rp 2.000

Total Pembayaran: Rp 152.000

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[Lihat Detail Order di Admin]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## Troubleshooting

### Email Not Sent

1. **Check Queue Worker Running**
   ```bash
   php artisan queue:work
   ```

2. **Check Failed Jobs**
   ```bash
   php artisan queue:failed
   ```

3. **Check Email Configuration**
   - Verify MAIL_ADMIN is set
   - Test with tinker

4. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Email Goes to Spam

1. Verify domain with Brevo
2. Setup SPF/DKIM records
3. Use verified sender address
4. Avoid spam trigger words

### Admin Not Receiving Email

1. Check MAIL_ADMIN in .env
2. Verify email address is correct
3. Check spam folder
4. Check failed jobs queue

## Security Considerations

- All emails sent via background queue
- Midtrans signature verified before processing
- Sensitive data not included in emails
- Admin emails contain full details for processing
- Customer emails contain minimal info for privacy
