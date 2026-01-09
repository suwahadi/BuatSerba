<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Masuk - BuatSerba')]
class Login extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    protected $messages = [
        'email.required' => 'Email harus diisi',
        'email.email' => 'Format email tidak valid',
        'password.required' => 'Password harus diisi',
    ];

    public function login()
    {
        $this->validate();

        // Rate limiting
        $throttleKey = strtolower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { 
                        message: 'Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.',
                        type: 'error'
                    }
                }))
            ");

            return;
        }

        // Attempt login with email
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            RateLimiter::clear($throttleKey);
            request()->session()->regenerate();

            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { 
                        message: 'Berhasil login! Mengalihkan...',
                        type: 'success'
                    }
                }))
            ");

            // Small delay for notification then redirect
            $this->js("setTimeout(() => window.location.href = '".route('dashboard')."', 1000)");

            return;
        }

        RateLimiter::hit($throttleKey);

        $this->js("
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { 
                    message: 'Email atau password yang Anda masukkan salah.',
                    type: 'error'
                }
            }))
        ");
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
