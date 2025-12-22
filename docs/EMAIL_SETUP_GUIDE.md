# Email Configuration Guide - Brevo (Sendinblue)

## Step 1: Mendapatkan SMTP Credentials dari Brevo

1. Buka https://www.brevo.com/ dan login (atau buat akun baru)
2. Masuk ke **Settings** > **SMTP & API**
3. Tab **SMTP**, klik **Create a new SMTP key** atau gunakan yang sudah ada
4. Copy informasi berikut:
   - SMTP Server: `smtp-relay.brevo.com`
   - Port: `587` (recommended for TLS)
   - Login: (email Anda)
   - Password/SMTP Key: (copy dari dashboard)

## Step 2: Update File .env

Tambahkan atau update konfigurasi berikut di file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-smtp-key-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@buatserba.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Catatan Penting:**
- `MAIL_USERNAME` = Email yang Anda gunakan untuk login ke Brevo
- `MAIL_PASSWORD` = SMTP Key yang Anda dapatkan dari dashboard (BUKAN password login)
- `MAIL_FROM_ADDRESS` = Email pengirim yang sudah diverifikasi di Brevo
- `MAIL_FROM_NAME` = Nama pengirim (biasanya nama aplikasi)

## Step 3: Verifikasi Domain/Email Pengirim (Opsional tapi Direkomendasikan)

1. Di dashboard Brevo, masuk ke **Senders** > **Domains**
2. Tambahkan domain Anda (misalnya: `buatserba.com`)
3. Tambahkan DNS records yang diberikan Brevo ke domain Anda:
   - SPF Record
   - DKIM Record
   - DMARC Record (optional)
4. Tunggu verifikasi (biasanya 24-48 jam)

## Step 4: Setup Queue Driver

Update konfigurasi queue di `.env`:

```env
QUEUE_CONNECTION=database
```

Jika belum ada tabel `jobs`, jalankan migration:

```bash
php artisan queue:table
php artisan migrate
```

## Step 5: Jalankan Queue Worker

**Development (Manual):**
```bash
php artisan queue:work
```

**Production (dengan Supervisor - Recommended):**

Buat file `/etc/supervisor/conf.d/buatserba-worker.conf`:

```ini
[program:buatserba-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/buatserba/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/buatserba/storage/logs/worker.log
stopwaitsecs=3600
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start buatserba-worker:*
```

## Step 6: Test Email

Test kirim email dengan tinker:

```bash
php artisan tinker
```

```php
use App\Models\Order;
use App\Jobs\SendOrderCreatedEmail;

$order = Order::first();
SendOrderCreatedEmail::dispatch($order);
```

Check queue:
```bash
php artisan queue:work --once
```

## Monitoring

**Cek failed jobs:**
```bash
php artisan queue:failed
```

**Retry failed jobs:**
```bash
php artisan queue:retry all
```

**Clear failed jobs:**
```bash
php artisan queue:flush
```

## Limits Brevo Free Plan

- 300 emails/day
- Unlimited contacts
- Brevo logo di email

Upgrade ke paid plan untuk:
- Lebih banyak email
- Hapus brevo logo
- Advanced features

## Troubleshooting

**Error: "Connection could not be established with host..."**
- Cek MAIL_HOST dan MAIL_PORT
- Pastikan firewall tidak block port 587

**Error: "Authentication failed"**
- Cek MAIL_USERNAME dan MAIL_PASSWORD
- Pastikan menggunakan SMTP Key, bukan password login

**Email tidak terkirim:**
- Cek queue worker berjalan: `php artisan queue:work`
- Cek failed jobs: `php artisan queue:failed`
- Cek log: `storage/logs/laravel.log`

**Email masuk spam:**
- Verifikasi domain
- Setup SPF, DKIM, DMARC
- Jangan kirim terlalu banyak email sekaligus
