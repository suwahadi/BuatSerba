<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Route::any('{any?}', function () {
//     return response()->json([
//         'message' => 'Website sedang dalam pengembangan',
//     ]);
// })->where('any', '.*');

Route::get('/', App\Livewire\Home::class)->name('home');

Route::get('/catalog', App\Livewire\Catalog::class)->name('catalog');
Route::get('/product/{slug}', App\Livewire\ProductDetail::class)->name('product.detail');
Route::get('/cart', App\Livewire\Cart::class)->name('cart');
Route::get('/checkout', App\Livewire\Checkout::class)->name('checkout');
Route::get('/payment/{code}', App\Livewire\Payment::class)->name('payment');
Route::get('/order/{orderNumber}', App\Livewire\OrderDetail::class)->name('order.detail');
Route::get('/payment/{code}/confirmation', App\Livewire\PaymentConfirmation::class)->name('payment.confirmation');
Route::get('/orders/{order}/print-invoice', App\Http\Controllers\OrderPrintInvoiceController::class)->name('orders.print-invoice');
Route::get('/orders/{order}/print-awb', App\Http\Controllers\OrderPrintAwbController::class)->name('orders.print-awb');

Route::get('/payment/{orderNumber}/success', function ($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.success');

Route::get('/payment/{orderNumber}/pending', function ($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.pending');

Route::get('/payment/{orderNumber}/failed', function ($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.failed');

Route::post('/midtrans/notification', [App\Http\Controllers\MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/midtrans/finish', [App\Http\Controllers\MidtransController::class, 'finish'])->name('midtrans.finish');
Route::get('/midtrans/unfinish', [App\Http\Controllers\MidtransController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/midtrans/error', [App\Http\Controllers\MidtransController::class, 'error'])->name('midtrans.error');

Route::middleware('guest')->group(function () {
    Route::get('/login', App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', App\Livewire\Auth\Register::class)->name('register');
});

Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/dashboard', App\Livewire\Dashboard\Index::class)->name('dashboard');
    Route::get('/order/{orderNumber}/rating', App\Livewire\Dashboard\OrderRating::class)->name('order.rating');
    Route::get('/profile', App\Livewire\Dashboard\Profile::class)->name('user.profile');
    Route::get('/address', App\Livewire\Dashboard\Address::class)->name('user.address');
    Route::get('/balance', App\Livewire\Dashboard\MemberBalance::class)->name('user.balance');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Berhasil logout');
})->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Dynamic Page Route
Route::get('/{slug}', App\Livewire\SlugRouter::class)->name('page.show');
