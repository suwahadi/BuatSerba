# Shopping Cart System - BuatSerba

## 📋 Overview
Sistem keranjang belanja yang lengkap telah berhasil diimplementasikan dengan menggunakan Livewire dan mengadaptasi design dari `resources/concept/cart.html`.

## ✅ Fitur yang Sudah Diimplementasikan

### 1. **Cart Management**
- ✅ Add to cart dari halaman product detail
- ✅ Update quantity (increment/decrement/manual input)
- ✅ Remove individual items
- ✅ Clear entire cart
- ✅ Stock validation real-time
- ✅ Duplicate item detection (auto-update quantity)

### 2. **Session Management**
- ✅ Support untuk guest users (session-based)
- ✅ Support untuk authenticated users
- ✅ Persistent cart across page reloads
- ✅ Automatic session ID generation

### 3. **Price Calculation**
- ✅ **Subtotal**: Total harga semua item
- ✅ **Shipping Cost**: 
  - GRATIS untuk pembelian ≥ Rp 500.000
  - Rp 25.000 untuk pembelian < Rp 500.000
- ✅ **Service Fee**: Rp 2.000 (fixed)
- ✅ **Discount**: Dari promo code
- ✅ **Total**: Subtotal + Shipping + Service Fee - Discount

### 4. **Promo Code System**
Kode promo yang tersedia:
- **DISKON10**: Diskon 10%
- **DISKON50K**: Diskon Rp 50.000
- **WELCOME**: Diskon 5%

### 5. **UI/UX Features**
- ✅ Empty cart state dengan call-to-action
- ✅ Cart item count badge di navigation
- ✅ Product image dengan fallback
- ✅ Variant display (warna, kapasitas, dll)
- ✅ Stock warning untuk item dengan stok < 5
- ✅ Free shipping progress indicator
- ✅ Loading states untuk semua actions
- ✅ Flash messages (success/error)
- ✅ Confirmation dialogs untuk delete actions

### 6. **Responsive Design**
- ✅ Mobile-first approach
- ✅ Adaptive layout untuk tablet dan desktop
- ✅ Touch-friendly controls
- ✅ Sticky order summary sidebar

## 🗂️ File Structure

```
app/
├── Livewire/
│   ├── Cart.php                    # Cart controller
│   └── ProductDetail.php           # Updated with add to cart
├── Models/
│   └── CartItem.php                # Cart item model
database/
├── migrations/
│   └── 2025_11_24_072531_create_cart_items_table.php
resources/
└── views/
    └── livewire/
        ├── cart.blade.php          # Cart view
        └── product-detail.blade.php # Updated with cart integration
routes/
└── web.php                         # Route: /cart
```

## 💾 Database Schema

### `cart_items` Table
```sql
- id (bigint, primary key)
- session_id (string, indexed) - For guest users
- user_id (bigint, nullable) - For logged in users
- product_id (bigint, foreign key)
- sku_id (bigint, foreign key)
- quantity (integer, default: 1)
- price (decimal 15,2) - Price snapshot at time of adding
- timestamps
```

## 🔧 Key Methods

### Cart.php
```php
// Cart Management
- getCartItemsProperty() - Get all cart items
- updateQuantity($cartItemId, $quantity) - Update item quantity
- incrementQuantity($cartItemId) - Increase quantity by 1
- decrementQuantity($cartItemId) - Decrease quantity by 1
- removeItem($cartItemId) - Remove single item
- clearCart() - Remove all items

// Calculations
- getSubtotalProperty() - Calculate subtotal
- getTotalProperty() - Calculate final total
- calculateShipping() - Calculate shipping cost
- applyPromoCode() - Apply discount code

// Checkout
- checkout() - Proceed to checkout (placeholder)
```

### ProductDetail.php
```php
- addToCart() - Add product to cart with validation
- buyNow() - Add to cart and redirect to cart page
```

## 🚀 Usage

### Accessing Cart Page:
```
/cart
```

### Adding to Cart:
1. Buka halaman product detail: `/product/{slug}`
2. Pilih variant (jika ada)
3. Set quantity
4. Klik "Tambah ke Keranjang" atau "Beli Sekarang"

### Managing Cart:
1. Akses `/cart`
2. Update quantity dengan +/- buttons atau input manual
3. Remove item dengan tombol "Hapus"
4. Apply promo code di sidebar
5. Klik "Lanjut ke Pembayaran" untuk checkout

## 📊 Business Logic

### Stock Validation
```php
// Saat add to cart
if ($sku->stock_quantity < $quantity) {
    return error;
}

// Saat update quantity
if ($newQuantity > $sku->stock_quantity) {
    return error;
}
```

### Duplicate Item Handling
```php
// Check if item exists
$existingItem = CartItem::where('session_id', $sessionId)
    ->where('sku_id', $skuId)
    ->first();

if ($existingItem) {
    // Update quantity instead of creating new
    $existingItem->update([
        'quantity' => $existingItem->quantity + $newQuantity
    ]);
}
```

### Shipping Calculation
```php
if ($subtotal >= 500000) {
    $shippingCost = 0; // FREE
} else {
    $shippingCost = 25000;
}
```

## 🎨 Design Features

- **Glass morphism** navigation bar
- **Smooth transitions** untuk semua interactions
- **Color-coded messages** (green = success, red = error)
- **Quantity controls** dengan visual feedback
- **Progress indicators** untuk free shipping
- **Empty state** dengan ilustrasi SVG
- **Sticky sidebar** untuk order summary

## 🔄 Livewire Features Used

1. **Real-time Updates**: `wire:model.live` untuk quantity
2. **Event Handling**: `wire:click` untuk actions
3. **Loading States**: `wire:loading` untuk feedback
4. **Confirmation**: `wire:confirm` untuk delete actions
5. **Event Dispatching**: `$this->dispatch('cartUpdated')`
6. **Flash Messages**: `session()->flash()`

## 📱 Responsive Breakpoints

- **Mobile**: < 640px (stacked layout)
- **Tablet**: 640px - 1024px (2 columns)
- **Desktop**: > 1024px (2/3 + 1/3 layout)

## 🔜 Future Enhancements

- [ ] Multi-step checkout process (Shipping, Payment, Confirmation)
- [ ] Save cart for logged-in users
- [ ] Wishlist integration
- [ ] Recently viewed products
- [ ] Product recommendations in cart
- [ ] Multiple shipping addresses
- [ ] Gift wrapping options
- [ ] Order notes/comments
- [ ] Estimated delivery date
- [ ] Payment gateway integration (Midtrans)

## 🐛 Known Limitations

- Checkout process is placeholder (not yet implemented)
- No order history
- No email notifications
- No payment processing
- Promo codes are hardcoded (should be in database)

## 💡 Tips & Best Practices

### For Developers:
1. **Price Snapshot**: Cart menyimpan harga saat item ditambahkan untuk menghindari perubahan harga yang tidak diinginkan
2. **Session Management**: Gunakan `cart_session_id` untuk tracking guest carts
3. **Stock Validation**: Selalu validasi stok sebelum update quantity
4. **Event Dispatching**: Gunakan `cartUpdated` event untuk sync cart count di navigation

### For Users:
1. **Free Shipping**: Belanja minimal Rp 500.000 untuk gratis ongkir
2. **Promo Codes**: Coba kode DISKON10, DISKON50K, atau WELCOME
3. **Stock Warning**: Perhatikan peringatan stok terbatas
4. **Variant Selection**: Pastikan memilih variant yang tepat sebelum add to cart

## 🔐 Security Considerations

- ✅ Session hijacking protection via Laravel's built-in session management
- ✅ CSRF protection via Livewire
- ✅ SQL injection protection via Eloquent ORM
- ✅ XSS protection via Blade templating
- ✅ Stock validation to prevent overselling

## 📈 Performance Optimization

- **Eager Loading**: `with(['product', 'sku'])` untuk menghindari N+1 queries
- **Computed Properties**: `getCartItemsProperty()` untuk caching
- **Debounced Input**: Quantity input dengan debounce 500ms
- **Indexed Columns**: `session_id` dan `user_id` untuk faster queries

---

**Created**: 2025-11-24
**Version**: 1.0
**Status**: ✅ Production Ready (Cart Management)
**Next**: Checkout Process Implementation
