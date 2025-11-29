<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/catalog', App\Livewire\Catalog::class)->name('catalog');
Route::get('/product/{slug}', App\Livewire\ProductDetail::class)->name('product.detail');
Route::get('/cart', App\Livewire\Cart::class)->name('cart');
Route::get('/checkout', App\Livewire\Checkout::class)->name('checkout');
Route::get('/payment/{code}', App\Livewire\Payment::class)->name('payment');
Route::get('/order/{orderNumber}', App\Livewire\OrderDetail::class)->name('order.detail');

// Payment Routes
Route::get('/payment/{orderNumber}/success', function($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.success');

Route::get('/payment/{orderNumber}/pending', function($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.pending');

Route::get('/payment/{orderNumber}/failed', function($orderNumber) {
    return redirect()->route('payment', $orderNumber);
})->name('payment.failed');

// Midtrans Routes
Route::post('/midtrans/notification', [App\Http\Controllers\MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/midtrans/finish', [App\Http\Controllers\MidtransController::class, 'finish'])->name('midtrans.finish');
Route::get('/midtrans/unfinish', [App\Http\Controllers\MidtransController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/midtrans/error', [App\Http\Controllers\MidtransController::class, 'error'])->name('midtrans.error');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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