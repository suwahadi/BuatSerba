# Member Balance Ledger – Implementasi TODO

## Tujuan

Modul **Member Balance Ledger** akan menambah metode pembayaran baru untuk aplikasi BuatSerba.  
Saldo berasal dari cashback atau voucher dan **tidak** bisa ditarik; hanya dapat digunakan untuk membayar pesanan.  
Implementasi harus aman (menggunakan transaksi atomik, idempoten, dan locking pesimistis) dan database‑nya harus skalabel.

## Daftar Tugas

Setiap tugas berikut diberi kode unik untuk memudahkan referensi dan memiliki bagian *Ketergantungan*, *Deskripsi*, *Berkas*, dan *Kriteria Penerimaan*.

### T001: Database Migrations

#### Ketergantungan
- Tidak ada.

#### Deskripsi
- Buat migrasi **`create_member_wallets_table`** dengan kolom berikut:
  - `id`.
  - `user_id` – foreign key ke tabel `users` (unik).
  - `balance` – `decimal(15,2)`, default `0`.
  - `locked_balance` – `decimal(15,2)`, default `0`.
  - Timestamps (`created_at`, `updated_at`).
- Buat migrasi **`create_member_balance_ledgers_table`** dengan kolom:
  - `id`.
  - `user_id` – foreign key ke tabel `users`.
  - `type` – enum (`credit`, `debit`).
  - `source_type` – enum (`voucher_cashback`, `order_payment`, `order_cancellation_refund`, `admin_credit`, `admin_debit`).
  - `source_id` – `unsignedBigInt` nullable (merujuk ke entitas terkait).
  - `amount` – `decimal(15,2)`.
  - `balance_before` – `decimal(15,2)`.
  - `balance_after` – `decimal(15,2)`.
  - `description` – string.
  - `reference_code` – string (unik) sebagai idempotency key.
  - `expires_at` – timestamp nullable.
  - Timestamps.
  - Index pada kolom `user_id` dan `created_at`.
- Buat migrasi **`add_cashback_fields_to_vouchers_table`** yang menambahkan kolom:
  - `cashback_type` – enum (`none`, `member_balance`), default `'none'`.
  - `cashback_amount` – `decimal(12,2)`, default `0`.
  - `cashback_percentage` – `decimal(5,2)`, default `0`.
- Setelah membuat ketiga migrasi, jalankan `php artisan migrate` untuk menerapkan perubahan skema.

#### Berkas
- Berkas migrasi dalam direktori `database/migrations/`.

#### Kriteria Penerimaan
- Semua migrasi berjalan tanpa error dan kolom/indeks sesuai spesifikasi di atas.

---

### T002: Model Layer

#### Ketergantungan
- **T001** harus selesai terlebih dahulu (migrasi tersedia).

#### Deskripsi
- Buat model **`MemberWallet`** (`app/Models/MemberWallet.php`):
  - Relasi: `belongsTo(User)`, `hasMany(MemberBalanceLedger)`.
  - Casting: `balance` dan `locked_balance` ke decimal.
  - Atribut tambahan: `available_balance` dihitung sebagai `balance - locked_balance`.
- Buat model **`MemberBalanceLedger`** (`app/Models/MemberBalanceLedger.php`):
  - Relasi: `belongsTo(User)`.
  - Casting: `amount`, `balance_before`, `balance_after` ke decimal.
  - Scope query: `credits()`, `debits()`, `forUser()` untuk memudahkan pencarian.
- Update **`User`** model (`app/Models/User.php`):
  - Tambahkan relasi `hasOne(MemberWallet)` dan `hasMany(MemberBalanceLedger)`.
- Update **`Voucher`** model (`app/Models/Voucher.php`):
  - Casting kolom cashback yang ditambahkan pada migrasi.
  - Tambahkan helper method `hasCashback(): bool` untuk mengecek apakah voucher memberikan cashback.

#### Berkas
- `app/Models/MemberWallet.php`
- `app/Models/MemberBalanceLedger.php`
- `app/Models/User.php`
- `app/Models/Voucher.php`

#### Kriteria Penerimaan
- Semua model dapat diinstansiasi melalui tinker atau unit test tanpa error.
- Relasi antar model berjalan sesuai definisi.

---

### T003: MemberWalletService

#### Ketergantungan
- **T002** selesai (model tersedia).

#### Deskripsi
- Buat service **`MemberWalletService`** (`app/Services/MemberWalletService.php`) yang menyediakan operasi utama:
  - `getOrCreateWallet(User $user): MemberWallet` – membuat atau mengambil wallet pengguna.
  - `getBalance(User $user): float` – mengembalikan `available_balance`.
  - `credit(User $user, float $amount, string $sourceType, ?int $sourceId, string $description, string $referenceCode): MemberBalanceLedger` – menambah saldo:
    - Periksa apakah `referenceCode` sudah ada; bila iya, lempar `DuplicateTransactionException` (idempotensi).
    - Jalankan dalam `DB::transaction()` dengan `lockForUpdate()` pada baris wallet.
    - Perbarui `balance`, catat entri ledger dengan `balance_before` dan `balance_after`.
  - `debitForOrder(User $user, Order $order): void` – memotong saldo untuk order:
    - Kunci baris wallet dan cek `available_balance >= order->total`; jika tidak, lempar `InsufficientBalanceException`.
    - Kurangi `balance` dan tambahkan `locked_balance` sejumlah `total`.
    - Catat entri ledger tipe `debit` dengan `source_type: order_payment`.
  - `releaseOrderLock(Order $order): void` – mengembalikan saldo terkunci ketika order dibatalkan:
    - Kurangi `locked_balance` dan tambahkan kembali ke `balance`.
    - Catat entri ledger tipe `credit` dengan `source_type: order_cancellation_refund`.
  - `creditCashback(Order $order, Voucher $voucher): void` – menambah saldo dari cashback:
    - Hitung jumlah dari `cashback_amount` atau `cashback_percentage` × `subtotal`.
    - Gunakan `referenceCode = "cashback-order-{order->id}-voucher-{voucher->id}"` (idempoten).
    - Panggil `credit()` dengan parameter tersebut.
- Buat **custom exceptions** di `app/Exceptions/Wallet/`:
  - `InsufficientBalanceException.php`.
  - `DuplicateTransactionException.php`.

#### Berkas
- `app/Services/MemberWalletService.php`
- `app/Exceptions/Wallet/InsufficientBalanceException.php`
- `app/Exceptions/Wallet/DuplicateTransactionException.php`

#### Kriteria Penerimaan
- Service dapat diinstansiasi dan dipanggil di tinker/unit test.
- Operasi kredit/debit mencatat ledger dengan benar dan menjaga idempotensi serta konsistensi saldo.

---

### T004: Integrasi Checkout (Livewire)

#### Ketergantungan
- **T003** selesai (service tersedia).

#### Deskripsi
- Edit `app/Livewire/Checkout.php`:
  - Inject `MemberWalletService` melalui konstruktor.
  - Tambahkan properti `$memberBalance` yang diisi di metode `mount()` bagi user yang login.
  - Tambahkan opsi `'member_balance'` pada metode `paymentMethods()`.
  - Tambahkan validasi: jika `paymentMethod === 'member_balance'`, pastikan saldo mencukupi (`balance >= total`).
  - Pada blok `submit/placeOrder`, tambahkan cabang untuk `'member_balance'`:
    - Panggil `MemberWalletService::debitForOrder()` di dalam `DB::transaction()` yang sama dengan `createOrder()`.
    - Set `order.payment_status = 'paid'` dan `paid_at = now()`.
    - Panggil `InventoryService` untuk commit stok.
    - Tangani `InsufficientBalanceException` dengan menambah error validasi dan membatalkan transaksi.
- Edit `resources/views/livewire/checkout.blade.php`:
  - Tambahkan pilihan “Saldo Member (tersedia: Rp X)” di bagian metode pembayaran.
  - Tampilkan pesan peringatan jika saldo tidak mencukupi.

#### Berkas
- `app/Livewire/Checkout.php`
- `resources/views/livewire/checkout.blade.php`

#### Kriteria Penerimaan
- Pengguna dapat memilih pembayaran melalui saldo member.
- Jika saldo mencukupi, order langsung berstatus `paid` dan saldo terpotong sesuai.
- Jika saldo tidak cukup, pesan error muncul dan order tidak diproses.

---

### T005: Integrasi Cashback Voucher

#### Ketergantungan
- **T003** selesai (service tersedia).

#### Deskripsi
- Edit `app/Services/VoucherService.php`:
  - Tambahkan metode `processCashbackIfApplicable(Order $order): void`.
  - Ambil kode voucher dari order (pastikan ada kolom/relasi yang menyimpan voucher yang diterapkan). Jika belum ada, tambahkan kolom `applied_voucher_code` nullable pada tabel `orders` lewat migrasi.
  - Bila `voucher->hasCashback()` mengembalikan `true`, panggil `MemberWalletService::creditCashback($order, $voucher)`.
- Edit `app/Services/OrderService.php`:
  - Simpan `applied_voucher_code` ke order saat `createOrder()` jika voucher digunakan.
  - Setelah status order menjadi `paid`, panggil `VoucherService::processCashbackIfApplicable($order)`.
- Edit `app/Http/Controllers/MidtransController.php` (webhook):
  - Setelah `transaction_status` menjadi `settlement` atau `capture`, panggil `VoucherService::processCashbackIfApplicable($order)`.

#### Berkas
- `app/Services/VoucherService.php`
- `app/Services/OrderService.php`
- `app/Http/Controllers/MidtransController.php`
- (Jika perlu) migrasi untuk kolom `applied_voucher_code` di tabel `orders`.

#### Kriteria Penerimaan
- Saat order dibayar lunas menggunakan voucher cashback, saldo member bertambah sesuai perhitungan cashback.
- Fungsi ini idempoten: pemanggilan ulang tidak menambah saldo dua kali.

---

### T006: Integrasi Pembatalan Order

#### Ketergantungan
- **T003** selesai (service tersedia).

#### Deskripsi
- Cari semua titik di codebase yang menangani pembatalan order (misalnya di panel Filament admin atau komponen Livewire order detail).
- Tambahkan panggilan `MemberWalletService::releaseOrderLock($order)` di setiap titik pembatalan, **hanya** jika:
  - `payment_method === 'member_balance'`.
  - `payment_status !== 'paid'` (masih menunggu pembayaran sehingga saldo masih ter-lock).
  - `locked_balance > 0` untuk menghindari double release.

#### Berkas
- Aksi pada `OrderResource` di Filament.
- Komponen `app/Livewire/OrderDetail.php` atau file sejenis.

#### Kriteria Penerimaan
- Saat order yang dibayar dengan saldo member dibatalkan sebelum dibayar, saldo yang terkunci dikembalikan ke saldo tersedia.

---

### T007: Filament Admin Panel — MemberWalletResource

#### Ketergantungan
- **T002** selesai (model tersedia).

#### Deskripsi
- Buat resource **MemberWalletResource** untuk Filament (`app/Filament/Resources/MemberWalletResource.php`):
  - **Table view**: tampilkan nama user, email, `balance`, `locked_balance`, jumlah transaksi (ledger count), dan tanggal dibuat.
  - **Detail view**: tampilkan riwayat ledger user (paginated).
  - **Header actions**: tambahkan dua tombol:
    - “Tambah Kredit Manual” – form input `amount` dan `description`, memanggil `MemberWalletService::credit()` dengan `source_type = admin_credit`.
    - “Debit Manual” – form input `amount` dan `description`, memanggil debit manual dengan `source_type = admin_debit` setelah memeriksa saldo cukup.
  - Tambahkan filter (misalnya berdasarkan user atau rentang tanggal).

#### Berkas
- `app/Filament/Resources/MemberWalletResource.php` beserta sub‑pages (List, Create/Edit, View).

#### Kriteria Penerimaan
- Admin dapat melihat daftar wallet beserta saldo yang tersedia dan terkunci.
- Admin dapat melakukan kredit atau debit manual melalui UI Filament dan ledger tercatat.

---

### T008: Halaman Saldo untuk Customer

#### Ketergantungan
- **T002** selesai (model tersedia).

#### Deskripsi
- Buat komponen Livewire **`MemberBalance`** (`app/Livewire/MemberBalance.php`):
  - Inject `MemberWalletService`.
  - Muat wallet dan riwayat ledger pengguna yang sedang login (paginate 15 entri per halaman).
  - Jika tidak terautentikasi, redirect ke halaman login.
- Buat blade view `resources/views/livewire/member-balance.blade.php` untuk menampilkan:
  - **Saldo tersedia** dan **saldo terkunci** dalam sebuah kartu.
  - Tabel riwayat transaksi: kolom tanggal, keterangan, tipe (`+` kredit atau `-` debit), jumlah, dan saldo akhir.
  - Gunakan badge warna untuk membedakan kredit vs debit.
- Tambahkan route `GET /member/balance` di `routes/web.php` dengan middleware `auth`.
- Tambahkan link “Saldo Saya” pada dropdown navbar pengguna yang sudah login.

#### Berkas
- `app/Livewire/MemberBalance.php`
- `resources/views/livewire/member-balance.blade.php`
- `routes/web.php`
- Komponen navbar (untuk menambah link).

#### Kriteria Penerimaan
- Pengguna yang telah login dapat mengakses halaman saldo member.
- Saldo tersedia, saldo terkunci, dan riwayat transaksi ditampilkan dengan jelas dan paginasi berfungsi.

---

### T009: Voucher Cashback di Filament Voucher Management

#### Ketergantungan
- **T001** selesai (kolom voucher tersedia).

#### Deskripsi
- Edit resource voucher di Filament untuk menambahkan pengaturan cashback:
  - Tambahkan section “Pengaturan Cashback”.
  - Field `cashback_type` sebagai select (`None` atau `Kredit Saldo Member`).
  - Field `cashback_amount` yang muncul reaktif ketika tipe cashback fixed dipilih.
  - Field `cashback_percentage` yang muncul reaktif ketika tipe cashback percentage dipilih.
  - Tambahkan kolom `cashback_type` pada tabel list voucher agar admin dapat melihat dengan cepat apakah voucher memberikan cashback.

#### Berkas
- File resource voucher di `app/Filament/Resources/Vouchers/`.

#### Kriteria Penerimaan
- Admin dapat mengonfigurasi cashback untuk voucher saat membuat/menyunting voucher melalui UI Filament.

---

### T010: Validasi Akhir & Seeding Demo

#### Ketergantungan
- Semua tugas sebelumnya (**T004** hingga **T009**) selesai.

#### Deskripsi
- Update `database/seeders/DatabaseSeeder.php` untuk menyertakan beberapa wallet demo dengan saldo awal bagi akun customer yang sudah ada.
- Lakukan pengujian manual end‑to‑end:
  1. Login sebagai customer, buka halaman saldo (awal harus kosong).
  2. Admin menambah kredit manual melalui panel → saldo muncul pada pelanggan.
  3. Checkout menggunakan metode “Saldo Member” ketika saldo cukup → order berstatus `paid` dan saldo terpotong.
  4. Checkout ketika saldo tidak mencukupi → muncul pesan error.
  5. Buat voucher dengan cashback, lakukan order dengan voucher dan lunasi → saldo bertambah sesuai cashback.
  6. Batalkan order yang dibayar dengan saldo member sebelum pembayaran final → saldo terkunci dikembalikan.
  7. Pastikan proses di atas tidak menghasilkan exception di log produksi dan perilaku sesuai ekspektasi.

#### Berkas
- `database/seeders/DatabaseSeeder.php`

#### Kriteria Penerimaan
- Semua skenario manual berjalan sukses.
- Tidak ada error atau pengecualian tak terduga dalam log saat menguji.
