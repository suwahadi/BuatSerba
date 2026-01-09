<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Daftar - BuatSerba')]
class Register extends Component
{
    public $name = '';

    public $email = '';

    public $phone = '';

    public $password = '';

    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/|unique:users,phone',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'Nama lengkap harus diisi',
        'name.min' => 'Nama minimal 3 karakter',
        'email.required' => 'Email harus diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',
        'phone.regex' => 'Format nomor HP tidak valid',
        'phone.unique' => 'Nomor HP sudah terdaftar',
        'password.required' => 'Password harus diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
    ];

    public function register()
    {
        $this->validate();

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role' => 'regular',
            ]);

            Auth::login($user);

            $this->dispatch('notify', [
                'message' => 'Registrasi berhasil! Selamat datang di BuatSerba.',
                'type' => 'success',
            ]);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
