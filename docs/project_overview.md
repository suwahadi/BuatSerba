# Project Toko Online: BuatSerba
Website Toko Online Laravel 12 Livewire: Multi Cabang, Multi Price (Berdasarkan Role User: Reguler, Grosir), Payment & Logistik API Lokal (Rp)
### Deskripsi / Scope
Bangun aplikasi toko online berbasis Laravel 12 dan Livewire, scalable dan siap deploy untuk pasar Indonesia. Website mendukung banyak cabang/gudang, keranjang dan checkout, integrasi Midtrans Core API (payment gateway), Rajaongkir (logistik), login order guest/user, serta semua fitur utama toko modern dengan single currency Rupiah (Rp). Optimalkan untuk kebutuhan UMKM, retail, dan bisnis digital Indonesia.
### Fitur Utama yang Harus Dijelaskan
- Multi cabang/gudang: Produk terhubung ke cabang/gudang di database, transaksi checkout otomatis memilih stok dan ongkir berdasarkan cabang terdekat.
- Multi price: Harga berbeda berdasarkan role user (Reguler, Grosir).
- Fitur keranjang, checkout, \& order tracking: Cart interaktif via Livewire, proses checkout real-time dengan validasi data, histori pesanan \& notifikasi.
- Integrasi Midtrans Core API: Semua pembayaran diproses otomatis via Midtrans Core API dengan validasi status, callback, invoice digital, dan settlement ke admin serta user.
- Integrasi Rajaongkir API: Hitung ongkir otomatis berdasarkan alamat user \& cabang/gudang terdekat secara real time.
- Guest \& user login order: Support pembelian tanpa wajib akun, juga login/register user (Google, WhatsApp OTP). Guest order bisa tracking status pesanan via email/WA.
- Single currency Rupiah (Rp): Semua transaksi, harga, dan laporan menggunakan format Rupiah tanpa opsi multi mata uang. Format: Rp 123.000,00.
- UI dan flow checkout disesuaikan dengan kebiasaan pasar lokal Indonesia (payment, ekspedisi, notifikasi, admin panel). Referensi web: tokopedia.com.

### Spesifikasi Teknis
- Backend: Laravel 12 (PHP 8+), multi-model, scalable, modular.
- Frontend: Livewire untuk interaktif stateful UI, Tailwind CSS untuk styling modern dan responsif.
- Admin Panel: Filament / custom CRUD untuk kelola cabang/gudang, produk, log order, laporan penjualan.
- Payment Gateway: Midtrans Core API (start, finish, notification, refund callback).
- Logistik: Rajaongkir API (support ongkir dari cabang ke area pelanggan).
- User Auth: Laravel built-in, + guest checkout, + social login (Google, WA optional).
- Security: Middleware untuk order, akses data, dan audit.
- Notifikasi: Email (API Brevo), WhatsApp (opsional).
- Laporan & Analitik: Dasbor penjualan, stok, best seller, transaksi harian, mingguan, bulanan, tahunan.
- Format uang: Rupiah (Rp), 2 decimal, custom helper (contoh: Rp 125.000,00).

### Flow Bisnis
1. Admin registrasi cabang/gudang dan produk (SKU)
2. User/guest browsing produk, add to cart
3. Checkout: Pilih cabang/gudang terdekat otomatis, input alamat, validasi ongkir via Rajaongkir
4. Pembayaran via Midtrans Core API, verifikasi otomatis
5. Notifikasi status pesanan via email/WhatsApp
6. Admin monitoring order, stok, dan transaksi di dashboard Filament
7. User/guest bisa tracking pesanan tanpa login (link dari email/WA)
### Kriteria Sukses
- UI/UX modern, responsif, dinamis, mudah diakses mobile
- Coding scalable, mudah di-maintain, sesuai standar Laravel 12 \& Livewire
- Integrasi full API payment dan logistik
- Semua transaksi, harga tampil menggunakan mata uang Rupiah (Format Rp 123.000,00)
- Dokumentasi deployment dan setup jelas bagi developer junior \& tim bisnis

## **SKEMA DATABASE TOKO ONLINE - HIERARKI LENGKAP**
### **1. MODUL USER \& AUTHENTICATION**
```
users
├── id (PK, bigint)
├── name (string)
├── email (string, unique)
├── email_verified_at (timestamp, nullable)
├── password (string, nullable - untuk guest bisa kosong)
├── phone (string, nullable)
├── phone_verified_at (timestamp, nullable)
├── is_guest (boolean, default: false)
├── provider (string, nullable - google, whatsapp, dll)
├── provider_id (string, nullable)
├── avatar (string, nullable)
├── status (enum: active, inactive, banned)
├── last_login_at (timestamp, nullable)
├── timestamps (created_at, updated_at)
user_addresses
├── id (PK, bigint)
├── user_id (FK → users.id)
├── label (string - rumah, kantor, dll)
├── recipient_name (string)
├── phone (string)
├── province_id (integer - dari Rajaongkir)
├── province_name (string)
├── city_id (integer - dari Rajaongkir)
├── city_name (string)
├── city_type (string - Kabupaten/Kota)
├── subdistrict_id (integer, nullable - dari Rajaongkir)
├── subdistrict_name (string, nullable)
├── postal_code (string)
├── full_address (text)
├── is_primary (boolean, default: false)
└── timestamps
```

***
### **2. MODUL CABANG/GUDANG (MULTI-BRANCH)**
```
branches
├── id (PK, bigint)
├── code (string, unique - BDG001, JKT001)
├── name (string - Cabang Bandung, Cabang Jakarta)
├── phone (string)
├── email (string)
├── province_id (integer - untuk Rajaongkir)
├── province_name (string)
├── city_id (integer - untuk Rajaongkir)
├── city_name (string)
├── city_type (string)
├── subdistrict_id (integer, nullable)
├── subdistrict_name (string, nullable)
├── postal_code (string)
├── full_address (text)
├── is_active (boolean, default: true)
├── priority (integer, default: 0 - untuk sorting cabang terdekat)
├── timestamps
```

***
### **3. MODUL PRODUK**
```
categories
├── id (PK, bigint)
├── parent_id (FK → categories.id, nullable - untuk nested category)
├── name (string)
├── slug (string, unique)
├── description (text, nullable)
├── image (string, nullable)
├── is_active (boolean, default: true)
├── sort_order (integer, default: 0)
└── timestamps
products
├── id (PK, bigint)
├── category_id (FK → categories.id)
├── name (string)
├── slug (string, unique)
├── description (text, nullable)
├── short_description (text, nullable)
├── main_image (string, nullable)
├── is_active (boolean, default: true)
├── is_featured (boolean, default: false)
├── meta_title (string, nullable - SEO)
├── meta_description (text, nullable - SEO)
├── meta_keywords (text, nullable - SEO)
├── view_count (integer, default: 0)
├── timestamps
product_images
├── id (PK, bigint)
├── product_id (FK → products.id)
├── image_path (string)
├── sort_order (integer, default: 0)
└── timestamps

skus (Stock Keeping Unit)
├── id (PK, bigint)
├── product_id (FK → products.id)
├── sku (string, unique - PRD-001)
├── base_price (decimal 15,2 - harga dasar Rp)
├── selling_price (decimal 15,2 - harga jual aktif Rp)
├── weight (integer - gram, untuk ongkir)
├── length (decimal 8,2, nullable - cm)
├── width (decimal 8,2, nullable - cm)
├── height (decimal 8,2, nullable - cm)
├── is_active (boolean, default: true)
├── timestamps

branch_inventory (stok per cabang per SKU)
├── id (PK, bigint)
├── branch_id (FK → branches.id)
├── sku_id (FK → skus.id)
├── quantity_available (integer - stok tersedia)
├── quantity_reserved (integer - stok di-booking sementara di cart)
├── minimum_stock_level (integer - alert minimum)
├── reorder_point (integer - titik reorder)
├── timestamps
└── unique_index (branch_id, sku_id)
```
***
### **4. MODUL KERANJANG (CART)**
```
carts
├── id (PK, bigint)
├── user_id (FK → users.id, nullable - nullable untuk guest)
├── session_id (string, nullable - untuk guest tracking)
├── sku_id (FK → skus.id)
├── branch_id (FK → branches.id, nullable - cabang terpilih)
├── quantity (integer)
├── unit_price (decimal 15,2 - harga saat add to cart Rp)
├── subtotal (decimal 15,2 - quantity * unit_price Rp)
├── notes (text, nullable)
├── timestamps
└── unique_index (user_id/session_id, sku_id)
```

***
### **5. MODUL TRANSAKSI \& ORDER**
```
orders
├── id (PK, bigint)
├── order_number (string, unique - ORD-20251108-001)
├── user_id (FK → users.id, nullable)
├── guest_email (string, nullable - untuk guest order)
├── guest_phone (string, nullable)
├── guest_name (string, nullable)
├── guest_tracking_token (string, unique, nullable - token tracking untuk guest)
├── branch_id (FK → branches.id - cabang pengiriman)
├── status (enum: pending, confirmed, processing, shipped, delivered, completed, cancelled, refunded)
├── payment_status (enum: unpaid, pending, paid, failed, refunded)
├── subtotal (decimal 15,2 - subtotal produk Rp)
├── shipping_cost (decimal 15,2, default: 0 - ongkir Rp)
├── tax_amount (decimal 15,2, default: 0 - pajak jika ada Rp)
├── grand_total (decimal 15,2 - total akhir Rp)
├── currency (string, default: 'Rp')
├── notes (text, nullable)
├── admin_notes (text, nullable)
├── timestamps
order_items
├── id (PK, bigint)
├── order_id (FK → orders.id)
├── sku_id (FK → skus.id)
├── product_name (string - snapshot saat order)
├── sku (string - snapshot)
├── quantity (integer)
├── unit_price (decimal 15,2 - harga saat beli Rp)
├── subtotal (decimal 15,2 - (unit_price * quantity) Rp)
└── timestamps
order_shipping_info
├── id (PK, bigint)
├── order_id (FK → orders.id, unique)
├── recipient_name (string)
├── phone (string)
├── province_id (integer)
├── province_name (string)
├── city_id (integer)
├── city_name (string)
├── city_type (string)
├── subdistrict_id (integer, nullable)
├── subdistrict_name (string, nullable)
├── postal_code (string)
├── full_address (text)
├── courier_code (string - jne, jnt, pos)
├── courier_service (string - REG, YES, OKE)
├── courier_service_description (text, nullable)
├── estimated_delivery_days (string - 1-2 hari)
├── shipping_cost (decimal 15,2 Rp)
├── tracking_number (string, nullable)
├── shipped_at (timestamp, nullable)
├── delivered_at (timestamp, nullable)
└── timestamps
order_status_histories
├── id (PK, bigint)
├── order_id (FK → orders.id)
├── from_status (string, nullable)
├── to_status (string)
├── notes (text, nullable)
├── changed_by (FK → users.id, nullable - admin yang ubah)
├── timestamps
```

***
### **6. MODUL PAYMENT (MIDTRANS INTEGRATION)**
```
payments
├── id (PK, bigint)
├── order_id (FK → orders.id)
├── payment_gateway (string, default: 'midtrans')
├── transaction_id (string, unique - dari Midtrans)
├── transaction_time (timestamp)
├── transaction_status (string - pending, settlement, capture, deny, cancel, expire, refund)
├── fraud_status (string, nullable - accept, deny, challenge)
├── payment_type (string - credit_card, bank_transfer, gopay, shopeepay, dll)
├── payment_channel (string, nullable - bca_va, bni_va, dll)
├── gross_amount (decimal 15,2 Rp)
├── currency (string, default: 'Rp')
├── signature_key (text, nullable)
├── status_code (string, nullable)
├── status_message (text, nullable)
├── midtrans_response (json, nullable - full response)
├── snap_token (text, nullable - token untuk Snap UI)
├── snap_redirect_url (text, nullable)
├── paid_at (timestamp, nullable)
├── expired_at (timestamp, nullable)
├── refunded_at (timestamp, nullable)
├── refund_amount (decimal 15,2, nullable Rp)
├── timestamps
payment_notifications (callback dari Midtrans)
├── id (PK, bigint)
├── payment_id (FK → payments.id, nullable)
├── order_id (string)
├── transaction_status (string)
├── notification_body (json - full callback payload)
├── processed (boolean, default: false)
├── processed_at (timestamp, nullable)
└── timestamps
```

***
### **7. MODUL NOTIFIKASI**
```
notifications
├── id (PK, bigint)
├── user_id (FK → users.id, nullable)
├── guest_email (string, nullable)
├── guest_phone (string, nullable)
├── type (enum: order_confirmation, payment_success, order_shipped, order_delivered, general)
├── channel (enum: email, whatsapp, sms, in_app)
├── title (string)
├── message (text)
├── data (json, nullable - data tambahan)
├── sent_at (timestamp, nullable)
├── read_at (timestamp, nullable)
├── failed_at (timestamp, nullable)
├── failure_reason (text, nullable)
└── timestamps
```
***
### **8. MODUL WISHLIST \& REVIEW (OPTIONAL PHASE 2)**
```
wishlists
├── id (PK, bigint)
├── user_id (FK → users.id)
├── sku_id (FK → skus.id)
└── timestamps
product_reviews
├── id (PK, bigint)
├── product_id (FK → products.id)
├── user_id (FK → users.id)
├── order_id (FK → orders.id)
├── rating (integer 1-5)
├── title (string, nullable)
├── review (text)
├── images (json, nullable - array foto review)
├── is_verified_purchase (boolean, default: false)
├── is_approved (boolean, default: false)
├── helpful_count (integer, default: 0)
└── timestamps
```

***
## **RELASI UTAMA (RELATIONSHIPS)**
```
User → hasMany → Orders
User → hasMany → UserAddresses
User → hasMany → Carts
User → hasMany → Wishlists
Branch → hasMany → BranchInventory
Branch → hasMany → Orders
Product → belongsTo → Category
Product → hasMany → SKUs
Product → hasMany → ProductImages

SKU → belongsTo → Product
SKU → hasMany → BranchInventory
SKU → hasMany → OrderItems
Category → hasMany → Products
Category → belongsTo → Category (parent)
Category → hasMany → Categories (children)
Order → belongsTo → User
Order → belongsTo → Branch
Order → hasMany → OrderItems
Order → hasOne → OrderShippingInfo
Order → hasMany → OrderStatusHistories
Order → hasMany → Payments
Payment → belongsTo → Order
Payment → hasMany → PaymentNotifications

```

***
## **INDEXES PENTING UNTUK PERFORMA**
```
users: index(email), index(phone), index(is_guest)
orders: index(order_number), index(user_id), index(status), index(payment_status), index(guest_tracking_token)
order_items: index(order_id), index(sku_id)
payments: index(transaction_id), index(order_id), index(transaction_status)
products: index(slug), index(category_id), index(is_active)
skus: index(sku), index(product_id)
branch_inventory: unique_index(branch_id, sku_id), index(quantity_available)
carts: index(user_id), index(session_id), index(sku_id)
```

***
## **CATATAN IMPLEMENTASI**
**Format Mata Uang:**
- Semua field harga menggunakan `decimal(15,2)` untuk Rp (max 9.999.999.999.999,99)
- Helper format: `Rp X.XXX.XXX` menggunakan `number_format()` PHP
**Guest Order Tracking:**
- Guest order menggunakan `guest_tracking_token` (UUID/random string)
- Email/WhatsApp berisi link: `{domain}/track/{token}`
- Tanpa perlu login untuk cek status
**Multi-Cabang Logic:**
- Checkout otomatis pilih cabang terdekat berdasarkan `city_id` user dengan `city_id` branch
- Jika multi branch punya stok, prioritas berdasarkan `branches.priority` dan jarak geografis

**Midtrans Integration:**
- Simpan `snap_token` untuk re-open payment jika user close
- Handle callback di `payment_notifications` table
- Update `order.payment_status` berdasarkan `transaction_status`
**Inventory Management:**
- Reserve stock saat add to cart (`quantity_reserved`)
- Release reserved stock jika cart expired (30 menit)
- Kurangi stok final setelah payment settlement
***
Skema ini **scalable, modular, dan production-ready** untuk pasar Indonesia dengan support penuh Rp, multi-cabang, payment gateway lokal, dan tracking guest order. Siap untuk implementasi Laravel 12 migration \& model relationships.