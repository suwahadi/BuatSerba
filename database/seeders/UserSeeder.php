<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin BuatSerba',
                'email' => 'admin@buatserba.com',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role' => 'admin',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'role' => 'wholesale',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'role' => 'wholesale',
                'is_guest' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567895',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Guest User 1',
                'email' => 'guest1@example.com',
                'password' => null,
                'phone' => '081234567896',
                'role' => 'regular',
                'is_guest' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Guest User 2',
                'email' => 'guest2@example.com',
                'password' => null,
                'phone' => '081234567897',
                'role' => 'regular',
                'is_guest' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Maria Google',
                'email' => 'maria@gmail.com',
                'password' => null,
                'phone' => '081234567898',
                'role' => 'regular',
                'is_guest' => false,
                'provider' => 'google',
                'provider_id' => '123456789',
                'status' => 'active',
            ],
            [
                'name' => 'Ahmad WhatsApp',
                'email' => 'ahmad@example.com',
                'password' => null,
                'phone' => '081234567899',
                'role' => 'regular',
                'is_guest' => false,
                'provider' => 'whatsapp',
                'provider_id' => '987654321',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
