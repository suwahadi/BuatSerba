<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Profil Saya - BuatSerba')]
class Profile extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    
    // Password fields
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|min:10|max:15',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|required_with:current_password|min:8|confirmed',
            'new_password_confirmation' => 'nullable|required_with:new_password',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama lengkap harus diisi',
        'name.min' => 'Nama lengkap minimal 3 karakter',
        'email.required' => 'Email harus diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'phone.min' => 'Nomor telepon minimal 10 digit',
        'phone.max' => 'Nomor telepon maksimal 15 digit',
        'current_password.required_with' => 'Password lama harus diisi untuk mengubah password',
        'current_password.current_password' => 'Password lama tidak sesuai',
        'new_password.required_with' => 'Password baru harus diisi',
        'new_password.min' => 'Password baru minimal 8 karakter',
        'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        'new_password_confirmation.required_with' => 'Konfirmasi password harus diisi',
    ];

    public function updateProfile()
    {
        $this->validate();

        try {
            $user = auth()->user();
            
            // Update basic info
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            // Update password if provided
            if (!empty($this->new_password)) {
                $user->update([
                    'password' => Hash::make($this->new_password)
                ]);
                
                // Clear password fields after successful update
                $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
                
                $this->dispatch('notify-success', message: 'Profil dan password berhasil diperbarui!');
            } else {
                $this->dispatch('notify-success', message: 'Profil berhasil diperbarui!');
            }

        } catch (\Exception $e) {
            $this->dispatch('notify-error', message: 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dashboard.profile');
    }
}
